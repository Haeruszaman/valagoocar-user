<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_merk extends Model {

    protected $table = 'm_merk';
    public $timestamps = false;
    protected $fillable = ['name'];

    public function car()
    {
        return $this->hasMany('App\Models\M_car', 'merk', 'name');
    }

}