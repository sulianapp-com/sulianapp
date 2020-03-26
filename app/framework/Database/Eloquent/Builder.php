<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/20
 * Time: 上午9:19
 */
namespace app\framework\Database\Eloquent;
use app\framework\Pagination\LengthAwarePaginator;
use BadMethodCallException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;

class Builder extends \Illuminate\Database\Eloquent\Builder
{

    public function expansionEagerLoadRelations(array $models)
    {
        foreach ($this->eagerLoad as $name => $constraints) {
            // For nested eager loads we'll skip loading them here and they will be set as an
            // eager load on the query to retrieve the relation so that they will be eager
            // loaded on that query, because that is where they get hydrated as models.
            if (strpos($name, '.') === false) {
                $models = $this->expansionLoadRelation($models, $name, $constraints);
            }
        }

        return $models;
    }

    /**
     * @param array $models
     * @param string $name
     * @param \Closure $constraints
     * @return array
     */
    protected function expansionLoadRelation(array $models, $name, \Closure $constraints)
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->expansionGetRelation($name);
        $relation->addEagerConstraints($models);

        $constraints($relation);

        $models = $relation->initRelation($models, $name);

        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.
        $results = $relation->expansionGet();

        return $relation->match($models, $results, $name);
    }
    public function expansionGetRelation($name){
        // We want to run a relationship query without any constrains so that we will
        // not have to remove these where clauses manually which gets really hacky
        // and is error prone while we remove the developer's own where clauses.
        $relation = Relation::noConstraints(function () use ($name) {
            try {
                if(method_exists($this->getModel(),$name)){
                    $model = $this->getModel()->$name();
                }else{
                    $model = $this->getModel()->expansionMethod($name,get_class($this->getModel()));
                }
                return $model;

            } catch (BadMethodCallException $e) {
                throw RelationNotFoundException::make($this->getModel(), $name);
            }
        });

        $nested = $this->nestedRelations($name);

        // If there are nested relationships set on the query, we will put those onto
        // the query instances so that they can be handled after this relationship
        // is loaded. In this way they will all trickle down as they are loaded.
        if (count($nested) > 0) {
            $relation->getQuery()->with($nested);
        }

        return $relation;
    }

    public function expansionGet($columns = ['*'])
    {
        $builder = $this->applyScopes();

        $models = $builder->getModels($columns);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
        if (count($models) > 0) {
            $models = $builder->expansionEagerLoadRelations($models);
        }

        return $builder->getModel()->newCollection($models);
    }

}