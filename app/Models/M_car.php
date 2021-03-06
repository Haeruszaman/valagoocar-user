<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_car extends Model {

    protected $table = 'm_car';
    public $timestamps = false;
    protected $fillable = ['name', 'merk', 'seat', 'loading', 'year'];

    public function merkOne()
    {
        return $this->hasOne('App\Models\M_merk', 'name', 'merk');
    }

}