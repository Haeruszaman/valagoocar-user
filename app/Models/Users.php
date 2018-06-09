<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model {

    protected $table = 'users';
    public $timestamps = false;
    protected $fillable = [
        'register_time', 
        'pin', 
        'image', 
        'username', 
        'name', 
        'city', 
        'address', 
        'email', 
        'password', 
        'phone', 
        'birthday', 
        'gender', 
        'status', 
        'roles',
        'secretcode',
        'remember_token'
    ];

    public function rolesOne()
    {
        return $this->hasOne('App\Models\User_roles', 'name', 'roles');
    }

}