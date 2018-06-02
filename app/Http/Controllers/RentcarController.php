<?php

namespace App\Http\Controllers;
use Nasution\ZenzivaSms\Client as Sms;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;

use Auth;

class RentcarController extends Controller
{
    use Helpers;

    protected function displayerors($validator)
    {
        $errorsmessage = [];
        $messages = $validator->messages();

        foreach ($messages->all(':message') as $message)
        {
            $errorsmessage[] = $message;
        }

        return $errorsmessage;
    }

    protected function responsejson($message, $status_code, $success, $code = false)
    {
        /*$status_code = "";
        if ($status_code == 'OK') {
            $status_code = 200;
        }elseif ($status_code == 'Created') {
            $status_code = 201;
        }elseif ($status_code == 'Accepted') {
            $status_code = 202;
        }elseif ($status_code == 'No Content') {
            $status_code = 204;
        }elseif ($status_code == 'Moved Permanently') {
            $status_code = 301;
        }elseif ($status_code == 'Found') {
            $status_code = 302;
        }elseif ($status_code == 'See Other') {
            $status_code = 303;
        }elseif ($status_code == 'Not Modified') {
            $status_code = 304;
        }elseif ($status_code == 'Temporary Redirect') {
            $status_code = 307;
        }elseif ($status_code == 'Bad Request') {
            $status_code = 400;
        }elseif ($status_code == 'Unauthorized') {
            $status_code = 401;
        }elseif ($status_code == 'Forbidden') {
            $status_code = 403;
        }elseif ($status_code == 'Not Found') {
            $status_code = 404;
        }elseif ($status_code == 'Method Not Allowed') {
            $status_code = 405;
        }elseif ($status_code == 'Not Acceptable') {
            $status_code = 406;
        }elseif ($status_code == 'Precondition Failed') {
            $status_code = 412;
        }elseif ($status_code == 'Unsupported Media Type') {
            $status_code = 415;
        }elseif ($status_code == 'Internal Server Error') {
            $status_code = 500;
        }elseif ($status_code == 'Not Implemented') {
            $status_code = 501;
        }*/
        
        return [
                'message'       => $message,
                'status_code'   => $status_code,
                'is_success'	=> $success,
                'id'	        => $code,
        ];
    }

    protected function sendSMS($nohp, $pesan)
    {
        $userkey = 'eum48h';
        $passkey = 'irfanhaerus11';
  
        //$url_sms = "https://reguler.zenziva.net/apps/smsapi.php?userkey=".$userkey."&passkey=".$passkey."&nohp=".$nohp."&pesan=".$pesan."";
         
        $sms = new Sms($userkey, $passkey);

        //Simple usage
        $response = $sms->send($nohp, $pesan);
    }

    protected function sendEmail($email, $subject, $compose_email)
    {
        
    }

    protected function generatedSecretcode()
    {
        $secretcode = str_random(100);
        return($secretcode);
    }

    protected function generatedCodeService()
    {
        $code = "S-".rand(10000,99999);
        return($code);
    }

    protected function generatedCodeOrder()
    {
        $code = "O-".rand(1000,9999);
        return($code);
    }
}