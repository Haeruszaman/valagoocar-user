<?php
namespace App\Transformer;
use App\Models\Users;
use League\Fractal\TransformerAbstract;

class UsersTransformer extends TransformerAbstract
{
    public function transform(Users $Users) {
        //date in mm/dd/yyyy format; or it can be in other formats as well
        $birthDate = date('d-m-Y', strtotime($Users->birthday));
        //dd(date("dm"));
        //explode the date to get month, day and year
        $birthDate = explode("-", $birthDate);
        //get age from date or birthdate
        $age = (date("dm", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("dm")
            ? ((date("Y") - $birthDate[2]) - 1)
            : (date("Y") - $birthDate[2]));

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
                'count' => $age,
            ],
            'gender'         => $Users->gender,
            'status' 		 => [
                'code' => $Users->status,
                'name' => $status
            ],
            
        ];        
    }
}