<?php

use Illuminate\Database\Seeder;


class YzpluginSeeder extends Seeder
{
    protected $table = 'yz_options';
    protected $uniTable = 'uni_account';

    public function __construct()
    {
        if (config('app.framework') == 'platform') {
            $this->uniTable = 'yz_uniacid_app';
        }
    }

    public function run()
    {

        $installed = app('plugins')->getPlugins();

        $is_plugin = \Illuminate\Support\Facades\DB::table($this->table)->where('option_name', 'test-plugins')->get();
        if ($is_plugin->isNotEmpty()) {
            return;
        }

        $plugins_enabled = \Illuminate\Support\Facades\DB::table($this->table)->where('option_name', 'plugins_enabled')->pluck("option_value")->first();
        $key = \Illuminate\Support\Facades\DB::table($this->table)->where('option_name', 'key')->pluck('option_value')->first();
        $market_source = \Illuminate\Support\Facades\DB::table($this->table)->where('option_name', 'market_source')->pluck("option_value")->first();


        $uniAccount = \Illuminate\Support\Facades\DB::table($this->uniTable)->get();

        $data[] = [
            'uniacid' => '0',
            'option_name' => 'test-plugins',
            'option_value' => 'true',
            'enabled' => '1',
        ];

        $data[] = [
            'uniacid' => '0',
            'option_name' => 'plugins-market',
            'option_value' => 'true',
            'enabled' => '1',
        ];
        $data[] = [
            'uniacid' => '0',
            'option_name' => 'market_source',
            'option_value' => $market_source,
            'enabled' => '1',
        ];
        $data[] = [
            'uniacid' => '0',
            'option_name' => 'key',
            'option_value' => $key,
            'enabled' => '1',
        ];
        $i = 4;
        foreach ($uniAccount as $u) {
            foreach ($installed as $key => $plugin) {
                if ($plugin['option_name'] == 'plugins_enabled') {
                    continue;
                }
                if ($plugin['option_name'] == 'market_source') {
                    continue;
                }
                if ($plugin['option_name'] == 'plugins-market') {
                    continue;
                }

                $data[$i] = [
                    'uniacid' => $u['uniacid'],
                    'option_name' => $key,
                    'option_value' => 'true',
                    'enabled' => 0,
                ];

                if (strpos($plugins_enabled, $key)) {
                    $data[$i]['enabled'] = 1;
                }
                $i++;
            }
        }

        \Illuminate\Support\Facades\DB::table($this->table)->delete();

        \Illuminate\Support\Facades\DB::table($this->table)->insert($data);
    }
}