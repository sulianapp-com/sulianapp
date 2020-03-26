<?php


namespace app\framework\Repository;


interface RepositorySourceInterface
{
    public function find($id);

    public function findMany($ids);

    public function findManyBy($key, $ids);

    public function findBy($key, $id);

    public function all();

    public function save($id, $data);

    public function saveBy($key, $id, $data);

    public function saveAll($data);

    public function saveMany($data);

    public function saveManyBy($key, $data);

    public function flush();

}