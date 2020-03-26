<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendMessage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $class = null;
    protected $func = null;
    protected $condition = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($class, $func, $condition)
    {
        $this->class = $class;
        $this->func = $func;
        $this->condition = $condition;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $builder = call_user_func_array([$this->class, $this->func], $this->condition);
        dd($builder);
        //file_put_contents("")
    }
}
