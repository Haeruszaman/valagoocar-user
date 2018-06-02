<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model {

    protected $table = 'users';
    public $timestamps = false;

    public function rolesOne()
    {
        return $this->hasOne('App\Models\User_roles', 'name', 'roles');
    }

}