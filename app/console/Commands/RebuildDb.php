<?php

namespace app\Console\Commands;


use app\backend\modules\member\models\Member;
use app\common\models\AccountWechats;
use app\frontend\modules\member\models\MemberUniqueModel;
use ClassesWithParents\D;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rebuildDb ';

    /**
     * The console command description.
     *
     * @var string
     */

    //重构数据库
    protected $description = 'To Rebuild The Database(重构数据库) ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

            //\Log::debug('数据重构1');
            //Schema::dropIfExists('yz_goods_param');

            \Log::debug('数据重构2');
            require_once '../../../install.php';

           \Log::debug('数据重构3');
            DB::select('truncate table'. DB::getTablePrefix().  '_migrations;');

           \Log::debug('数据重构4');
        \Artisan::call('migrate',['--force' => true]);

    }




}
