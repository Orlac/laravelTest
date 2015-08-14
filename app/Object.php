<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Object
 * @package Adventum
 *
 *
 * @property $status
 */
class Object extends Model
{

    const STATUS_NEW = 'NEW';
    const STATUS_PENDIG = 'PENDIG';
    const STATUS_DONE = 'DONE';
    const STATUS_ERROR = 'ERROR';

    /**
     * @var string
     */
    protected $table = 'objects';

    /**
     * @var array
     */
    protected $fillable = ['name'];


    public function createFullObject(){
        $waitTime = rand(0, 10);
        $this->setStatus(self::STATUS_PENDIG);
        sleep($waitTime);
        $error = (boolean) rand(0,1);
        $this->setStatus( ($error)? self::STATUS_ERROR : self::STATUS_DONE );

    }

    public function setStatus($status){
        $this->status = $status;
        $this->save();
    }
}
