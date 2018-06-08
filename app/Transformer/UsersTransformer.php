<?php
namespace App\Transformer;
use App\Models\Users;
use League\Fractal\TransformerAbstract;

class UsersTransformer extends TransformerAbstract
{
    public function transform(Users $Users) {
        $status = "";
        if($Users->status == 0) $status = "PENDING";
        else if($Users->status == -1) $status = "BANNED";
        else if($Users->status == 1) $status = "ACTIVE";

        return [
            'jointime'  => date('d-m-Y, H:i:s', strtotime($Users->register_time)),
            'image'          => $Users->image,
            'username'       => $Users->username,
            'name'           => $Users->name,
            'city'           => $Users->city,
            'address'        => $Users->address,
            'email'          => $Users->email,
            'phone'          => $Users->phone,
            'roles'          => [
                'id' => $Users->rolesOne == null ? "" : $Users->rolesOne->id,
                'name' => $Users->roles,
            ],
            'age'       => [
                'birthday' => date('d-m-Y', strtotime($Users->birthday)),
                'count' => '',
            ],
            'gender'         => $Users->gender,
            'status' 		 => [
                'code' => $Users->status,
                'name' => $status
            ],
            
        ];        
    }
}