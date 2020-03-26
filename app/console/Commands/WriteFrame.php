<?php

namespace app\console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class WriteFrame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'write:frame {file}';

    // protected $fileCount;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer the specified file to word';

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
        $value = (string)$this->argument('file');

        $finder = Finder::create()->in($this->destPath($value));

        $fileCount = $finder->count();

        $bar = $this->output->createProgressBar($fileCount);

        if (strpos($value, '/') !== false) {

            $all = explode('/', $value);
            $name = $all[count($all) -1];
        
        } else {

            $name = $value;
        }

        foreach ($finder as $file) {
           
            if ($file->isFile()) {

                $fileContent = $file->getContents();

                $this->writeWord($fileContent, $name);
            }
            $bar->advance();
        }
        $bar->finish();

        $this->comment('Importing Word Success');
    }

    private function writeWord($content, $name)
    {
        $fileName = base_path($name) . '.docx';

        // if (file_exists($fileName) && $this->fileCount > 1) {
            
        //     $ans = $this->ask('Do you wish to continue?');
            
        //     if (in_array($ans, ['yes', 'y'])) {
            
        //         file_put_contents($fileName, $content, FILE_APPEND);
        //     } else {

        //         exit();
        //     }

        // } else {

            file_put_contents($fileName, $content, FILE_APPEND);
        // }
    }
    
    /**
     * @return string
     */
    private function destPath($name)
    {
        $path = base_path($name);

        if (is_dir($path)) {
            return $path;
        }
        $this->error('Folder does not exist!');
        exit();
    }
}
