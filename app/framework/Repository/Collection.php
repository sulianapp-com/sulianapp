<?php


namespace app\framework\Repository;


class Collection extends \Illuminate\Support\Collection
{
    public function only($keys)
    {
        $result = new static();
        foreach ($this->items as $item){

            $result[] = array_filter($item, function ($val) use ($keys) {
                return in_array($val, $keys);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $result;
    }
}