<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
//use App\Object;
use DB;

class Object extends Command
{

    private $_timeEnd=0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'object:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_timeEnd = Config::get('app.maxCronTime') + time();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("start");
        $this->waitPid();
        $this->makePid();

        while(($object = $this->getObject()) && !$this->isEnd()){
            $this->process($object);
        }

        $this->unPid();
        $this->info("end ");
    }


    protected function getObject(){
        return \App\Object::where('status', \App\Object::STATUS_NEW)->orderBy('created_at')->first();
    }

    protected function process($object){

        try{
            $object->createFullObject();
            $this->info('object '.$object->id.' created ');
        }catch(\Exception $e){
            $this->error('object '.$object->id.' error: '.$e->getMessage());
        }

    }

    protected function waitPid(){
        while($this->isPid() && !$this->isEnd()){
            sleep(10);
        }
    }

    protected function makePid(){
        file_put_contents( base_path().'/.pid', time() );
    }

    protected function isPid(){
        return file_exists(base_path().'/.pid');
    }

    protected function unPid(){
        return unlink(base_path().'/.pid');
    }

    protected function isEnd(){
        return time() > $this->_timeEnd;
    }


}
