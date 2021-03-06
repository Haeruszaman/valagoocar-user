<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_city extends Model {

    protected $table = 'm_city';
    public $timestamps = false;
    protected $fillable = ['name'];

    public function service()
    {
        return $this->hasMany('App\Models\Service', 'city', 'name');
    }

}