<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $table = 'order';
    public $timestamps = false;

    public function userOne()
    {
        return $this->hasOne('App\Models\Users', 'username', 'user');
    }

    public function serviceOne()
    {
        return $this->hasOne('App\Models\Service', 'code', 'service_code');
    }

    public function carOne()
    {
        return $this->hasOne('App\Models\M_car', 'name', 'car');
    }

}