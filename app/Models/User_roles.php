<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_roles extends Model {

    protected $table = 'user_role';
    public $timestamps = false;
    protected $fillable = ['name'];

    public function user()
    {
        return $this->hasMany('App\Models\Users', 'roles', 'name');
    }

}