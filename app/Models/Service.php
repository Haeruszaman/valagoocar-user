<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {

    protected $table = 'service';
    public $timestamps = false;

    public function userOne()
    {
        return $this->hasOne('App\Models\Users', 'username', 'vendor');
    }

}