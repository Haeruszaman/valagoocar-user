<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $table = 'order';
    public $timestamps = false;
    protected $fillable = [
        'code', 
        'service_code', 
        'image', 
        'user', 
        'order_date', 
        'order_time', 
        'days', 
        'end_date', 
        'address_order', 
        'city', 
        'car', 
        'description', 
        'price_total', 
        'status'
    ];

    public function userOne()
    {
        return $this->hasOne('App\Models\Users', 'username', 'user');
    }

    public function serviceOne()
    {
        return $this->hasOne('App\Models\Service', 'code', 'service_code');
    }

}