<?php

use Illuminate\Database\Seeder;

class YzUserRoleSeeder extends Seeder
{
    protected $table = 'yz_user_role';
    protected $sourceTable = 'sz_yi_perm_role';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return;
        $roles = \Illuminate\Support\Facades\DB::table($this->sourceTable)
            ->where(['status'=>'1','deleted'=>'0' ])
            ->where('uniacid','>','0')
            ->get();

        if($roles){
            foreach($roles as $role){
               $exists = \Illuminate\Support\Facades\DB::table($this->table)
                   ->where(['uniacid'=>$role['uniacid'],'name'=>$role['rolename']])
                   ->exists();
               if(!$exists){
                    \Illuminate\Support\Facades\DB::table($this->table)->insert([
                        'name'=>$role['rolename'],
                        'uniacid'=>$role['uniacid'],
                        'created_at'=>time(),
                        'updated_at'=>time()
                    ]);
                    echo "迁移 uniacid:{$role['uniacid']} rolename:{$role['rolename']} 完成\n";
               }

                //@todo insert yz_permission
            }
        }

    }
}
