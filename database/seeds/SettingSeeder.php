<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_sysset';
    protected $newTable = 'yz_setting';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info('调试-SettingSeeder');
        return;
        if (!Schema::hasTable($this->oldTable)) {
            echo $this->oldTable." 不存在 跳过\n";
            return;
        }
       $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
       if($newList->isNotEmpty()){
          echo "yz_setting 已经有数据了跳过\n";
          return ;
       }

       $list =  \Illuminate\Support\Facades\DB::table($this->oldTable)->get();
       if($list){
           foreach ($list as $v){

                Setting::$uniqueAccountId = $v['uniacid'];
                if($v['sets']) {
                    $sets = @unserialize($this->_fixData($v['sets']));
                    if($sets) {
                        foreach ($sets as $k1 => $v1) {
                            Setting::set('shop.' . $k1, $v1);
                        }
                    }
                }
                if($v['plugins']) {
                    $plugins = @unserialize($this->_fixData($v['plugins']));
                    if($plugins) {
                        foreach ($plugins as $k2 => $v2) {
                            if(is_array($v2)) {
                                foreach ($v2 as $kk2 => $vv2) {
                                    Setting::set(($k2 ? : 'plugin') . '.' . $kk2, $vv2);
                                }
                            }else{
                                Setting::set('plugin.' . $k2, $v2);
                            }
                        }
                    }
                }
               if($v['sec']) {
                   $sec = @unserialize($this->_fixData($v['sec']));
                   if($sec) {
                       foreach ($sec as $k3 => $v3) {
                           Setting::set('pay.' . $k3, $v3);
                       }
                   }
               }
               echo "完成：uniacid:".$v['uniacid'] ."\n";
           }
       }
    }

    private function _fixData($badData)
    {
        $data = preg_replace_callback(
            '/(?<=^|\{|;)s:(\d+):\"(.*?)\";(?=[asbdiO]\:\d|N;|\}|$)/s',
            function($m){
                return 's:' . mb_strlen($m[2]) . ':"' . $m[2] . '";';
            },
            $badData
        );
        return $data;
    }
}
