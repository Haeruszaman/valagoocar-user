<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function displayerors($validator)
    {

        $errorsmessage = [];
        $messages = $validator->messages();

        //dd($messages);
        foreach ($messages->all(':message') as $message)
        {
            $errorsmessage[] = $message;
        }

        return $errorsmessage;
    }

    protected function responsejson($message, $status_code, $success, $code = false)
    {
                         
        return [
                'message'       => $message,
                'status_code'   => $status_code,
                'is_success'	=> $success,
                'id'	        => $code,
        ];
    }
}
