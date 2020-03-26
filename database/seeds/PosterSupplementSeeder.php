<?php

use Illuminate\Database\Seeder;

Class PosterSupplementSeeder extends Seeder
{
    public function run()
    {
        factory(Yunshop\Poster\models\PosterSupplement::class, 10)->create()->make();
    }
}