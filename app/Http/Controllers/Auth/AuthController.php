<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use DB, Hash, Mail; 
use Validator;
use Illuminate\Support\Facades\Password; 
use Illuminate\Mail\Message; 
use App\Models\Users;
use Auth; 


class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {

        $input = $request->only([

            'email',
            'password',

        ]);

        $validator = Validator::make($input,[
                
            'email' => 'required|email|max:255',
            'password' => 'required',

        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            // validasi 1. pengecekan data emailnya
            $user_count = Users::where('email',  $input['email'])->count();
            if ($user_count == 0) {
                $validator->errors()->add('email', 'Email Yang Anda Inputkan Salah Atau Belum Terdaftar');
            }else{
                $user = Users::where('email', $input['email'])->first();
                if ($user->status == 0) {
                    $validator->errors()->add('status', 'Anda Tidak Bisa Login Karena User Tersebut Belum Aktif, Silahkan Konfirmasi Ke Email Anda');
                }
            }
        });
        
        if( $validator->fails() == false ){
            
            try {
                // Attempt to verify the credentials and create a token for the user
                if (!$token = JWTAuth::attempt(
                    $this->getCredentials($request)
                )) {
                    return $this->onUnauthorized();
                }
            } catch (JWTException $e) {
                // Something went wrong whilst attempting to encode the token
                return $this->onJwtGenerationError();
            }
    
            // All good so return the token
            return $this->onAuthorized($token);

        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
            return response()->json($ket);
        }

    }

        /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    /*public function Login(Request $request)
    {

        $input = $request->only([

            'email',
            'password',

        ]);

        $validator = Validator::make($input,[
                
            'email' => 'required|email',
            'password' => 'required',

        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            // validasi 1. pengecekan data emailnya
            /*$user_count = Admin::where('phone',  $input['phone'])->count();
            if ($user_count == 0) {
                $validator->errors()->add('phone', 'Nomor Telepon Yang Anda Inputkan Salah Atau Belum Terdaftar');
            }else{
                $user = Admin::where('phone', $input['phone'])->first();
                if ($user->status == 0) {
                    $validator->errors()->add('status', 'Anda Tidak Bisa Login Karena User Tersebut Belum Aktif, Silahkan Konfirmasi Ke Email Anda');
                }
            }
        });
        
        if( $validator->fails() == false ){
            
            try {
                
                // Attempt to verify the credentials and create a token for the user
                if (!$token = JWTAuth::attempt(
                    $this->getCredentials($request)
                )) {
                    return $this->onUnauthorized();
                }
            } catch (JWTException $e) {
                // Something went wrong whilst attempting to encode the token
                return $this->onJwtGenerationError();
            }  
    
            // All good so return the token
            return $this->onAuthorized($token);

        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
            return response()->json($ket);
        } 

    }*/

    /**
     * What response should be returned on invalid credentials.
     *
     * @return JsonResponse
     */
    protected function onUnauthorized()
    {
        return new JsonResponse([
            'is_success' => false,
            'message' => 'Password Yang Anda Inputkan Salah, Silahkan Cek Kembali Password Anda'
        ], Response::HTTP_UNAUTHORIZED);
    }

    protected function onUserNotValid()
    {
        return new JsonResponse([
            'is_success' => false,
            'message' => 'Anda Tidak Bisa Login Karena User Tersebut Belum Aktif, Silahkan Konfirmasi Ke Email Anda',
        ]);
    }

    /**
     * What response should be returned on error while generate JWT.
     *
     * @return JsonResponse
     */
    protected function onJwtGenerationError()
    {
        return new JsonResponse([
            'message' => 'Email Yang Anda Inputkan Salah Atau Belum Terdaftar'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * What response should be returned on authorized.
     *
     * @return JsonResponse
     */
    protected function onAuthorized($token)
    {
        return new JsonResponse([
            'is_success' => true,
            'message' => 'token_generated',
            'data' => [
                'token' => $token,
            ]
        ]);
    }



    /**
     * Invalidate a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteInvalidate()
    {
        $token = JWTAuth::parseToken();

        $token->invalidate();

        return new JsonResponse(['message' => 'token_invalidated']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function patchRefresh()
    {
        $token = JWTAuth::parseToken();

        $newToken = $token->refresh();

        return new JsonResponse([
            'message' => 'token_refreshed',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }

    /**
     * Get authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUser()
    {
        return new JsonResponse([
            'message' => 'authenticated_user',
            'data' => JWTAuth::parseToken()->authenticate()
        ]);
    }
    
}