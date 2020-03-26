<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/20
 * Time: 上午9:37
 */

namespace app\framework\Database\Eloquent;


class Collection extends \Illuminate\Database\Eloquent\Collection
{
    public function expansionLoad($relations)
    {
        if (count($this->items) > 0) {
            if (is_string($relations)) {
                $relations = func_get_args();
            }
            /**
             * @var Builder $query
             */
            $query = $this->first()->newQuery()->with($relations);

            $this->items = $query->expansionEagerLoadRelations($this->items);
        }

        return $this;
    }
}