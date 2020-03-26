<?php

namespace app\Console\Commands;


use Illuminate\Console\Command;


class Test extends Command
{

    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

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

        dump(request()->getSchemeAndHttpHost());
    }

}
