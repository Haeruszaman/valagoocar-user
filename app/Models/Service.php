<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {

    protected $table = 'service';
    public $timestamps = false;
    protected $fillable = [
        'code', 
        'vendor', 
        'car', 
        'description', 
        'city', 
        'price', 
        'is_active', 
    ];

    public function userOne()
    {
        return $this->hasOne('App\Models\Users', 'username', 'vendor');
    }

    public function cityOne()
    {
        return $this->hasOne('App\Models\M_city', 'name', 'city');
    }

    public function carOne()
    {
        return $this->hasOne('App\Models\M_car', 'name', 'car');
    }

}