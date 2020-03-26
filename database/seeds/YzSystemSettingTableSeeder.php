<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class YzSystemSettingTableSeeder extends Seeder
{
    protected $table = 'yz_system_setting';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Schema::hasTable($this->table)) {
            echo $this->table." 不存在 跳过\n";
            return;
        }
        $table = DB::table($this->table)->where('key', 'global')->first();
        if($table){
            // 已经有数据了跳过
            echo $this->table." There's already data skipped.\n";
            return ;
        }

        $config['image_extentions'] = ['0' => 'gif', '1' => 'jpg', '2' => 'jpeg', '3' => 'png'];
        $config['image_limit'] = 5000;
        $config['audio_extentions'] = ['0' => 'mp3', '1' => 'mp4'];
        $config['audio_limit'] = 5000;
        $config['thumb_width'] = 800;
        $config['zip_percentage'] = 100;

        DB::table($this->table)->insert([
            'key' => 'global',
            'value' => serialize($config),
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }
}
