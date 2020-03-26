<?php

use Illuminate\Database\Seeder;

Class PosterSeeder extends Seeder
{
    public function run()
    {
        return;
        factory(Yunshop\Poster\models\Poster::class, 10)->create()->make();
//        factory(Yunshop\Poster\PosterModel::class, 2)->create()
//            ->each(function($u){
//                $u->supplement()->save(factory(Yunshop\Poster\PosterModel::class)->make());
//            });
//        factory('Yunshop\')
    }
}