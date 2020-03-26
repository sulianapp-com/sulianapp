<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/4
 * Time: 下午7:06
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ModelExpansion
{
    public function getRelationshipFromExpansion($method,Model $model){

        $relations = $this->$method($model);

        if (! $relations instanceof Relation) {
            throw new \LogicException('Relationship method must return an object of type '
                .'Illuminate\Database\Eloquent\Relations\Relation');
        }

        $model->setRelation($method, $results = $relations->getResults());

        return $results;
    }
}