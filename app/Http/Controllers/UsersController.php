<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Users;
use App\Models\User_roles;
use App\Transformer\UsersTransformer;
use Validator;
use Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends RentcarController
{

	public function index(Request $request)
    {
        $model = new Users;
        $queries = [];
        $columns_filter = ['filter_status' => "status", 'filter_roles' => "roles"];

        // FILTER
        foreach ($columns_filter as $key => $column) {
            if ($request->has($key)) {
                $model = $model->where($column, $request->$key);
                $queries[$key] = $request->$key;
            }
        }

        // searching
        if ($request->has('keyword') == true) {
         
            $model->Where("username", "like","%{$request->input('keyword')}%")
                  ->orWhere("name", "like","%{$request->input('keyword')}%")
                  ->orWhere("email", "like","%{$request->input('keyword')}%");
            $queries['keyword'] = $request->keyword;

        }

        // PAGING
        $result = $model->paginate(1000)->appends($queries);
 
        return $this->response->paginator($result, new UsersTransformer);
    }

	public function show($username)
    {
        $input = [];
        $input['username'] = $username;

        $validator = Validator::make($input,[   
            'username'         => 'required',
        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            
        });

        //End Validate
        if( $validator->fails() == false ){
            $model = Users::where('username', $username)->firstOrFail();
            return (new UsersTransformer)->transform($model);
        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }
    }

    public function update(Request $r)
    {
        $input = $r->only([
            'name',
            'username',
            'city',
            'address',
            'gender',
            'birthday',
        ]);

        $validator = Validator::make($input,[
                
            'name'      => 'required',
            'username'  => 'sometimes|required|unique:users',
            'city'      => 'required', 
            'address'   => 'required',
            'gender'    => 'required',
            'birthday'  => 'required',

        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            // validasi 1. yang bisa melakukan update hanya yang login
            $user = Users::where('username', Auth::user()->username)->first();
            if ($user->username != Auth::user()->username) {
                $validator->errors()->add('username', 'Anda Tidak Bisa Mengubah User');
            }
        });

        if( $validator->fails() == false ){

	        $model = Users::where('username', Auth::user()->username)->firstOrFail();
	        $model->name      = $r->input('name');
            $model->username  = $r->input('username');
            $model->city      = $r->input('city');
            $model->address   = $r->input('address');
            $model->gender    = $r->input('gender');
            $model->birthday  = date('Y-m-d', strtotime($r->input('birthday')));
        
            if($model->save() == true){

                $ket = $this->responsejson('Berhasil Tersimpan', 200, true, $model->username);

            }else{

                $ket = $this->responsejson('Tidak Berhasil Tersimpan', 200, false);

            }

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }  

        return response()->json($ket);
    }

    public function change_password(Request $r)
    {
        $input = $r->only([
            'password',
            'new_password',
            'repeat_new_password',
        ]);

        $validator = Validator::make($input,[
                
            'password'        	  => 'required|min:6|max:50',
            'new_password'        => 'required|min:6|max:50|different:password', 
            'repeat_new_password' => 'required|min:6|max:50|same:new_password',

        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            
        });

        if( $validator->fails() == false ){

	        $model = Users::where('username', Auth::user()->username)->firstOrFail();
	            //dd(Auth::user()->username);
	        $model->password = Hash::make($r->input('new_password'));
        
            if($model->save() == true){

                $ket = $this->responsejson('Berhasil Tersimpan', 200, true, $model->username);

            }else{

                $ket = $this->responsejson('Tidak Berhasil Tersimpan', 200, false);

            }

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }  

        return response()->json($ket);
    }

    public function reset_password(Request $r)
    {
        //inputannya phone
        $input = $r->only([
            'phone',
        ]);

        $validator = Validator::make($input,[
                
            'phone' => 'required|numeric',

        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            //validai 1. apakah nomer telepon tsb memiliki user
            $user = Users::where('phone', $input['phone'])->count();
            //dd($user->username);
            if ($user == 0){
                $validator->errors()->add('phone', 'Nomor Tersebut Tidak Memiliki Data Apapun / Kosong');
            }

        });
        // END VALIDATION

        if( $validator->fails() == false ){
            //mencari data di table users.
            $user = Users::where('phone', $r->input('phone'))->first();
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789?!';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < rand(5,8); $i++) {
                 $string .= $characters[mt_rand(0, $max)];
            }
            $user->password = Hash::make($string);
            $user->save();

            //1. mengirim SMS ke usernya.
            $nohp  = $user->phone;
            $pesan = 'Password Baru Anda Adalah '.$string;
            $this->sendSMS($nohp, $pesan);

            //dd('Password Baru Anda Adalah '.$string);
            $ket = $this->responsejson('SMS Berhasil Terkirim', 200, true, $user->username);

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }  

        return response()->json($ket);
    }

    public function request_forgot_password(Request $r)
    {
        //inputannya email
        $input = $r->only([
            'email',
        ]);

        $validator = Validator::make($input,[
                
            'email' => 'required|email',

        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            //validai 1. apakah email tsb memiliki user
            $user = Users::where('email', $input['email'])->count();
            //dd($user->username);
            if ($user == 0){
                $validator->errors()->add('email', 'Email Tersebut Tidak Memiliki Data Apapun / Kosong');
            }

        });
        // END VALIDATION

        if( $validator->fails() == false ){
            //mencari data di table users.
            $user = Users::where('email', $r->input('email'))->first();
            //1. mengirim Email ke usernya.
            $email  = $r->input('email');
            $subject = 'Confirm '.$r->input('email').' on Rentcar';
            $compose_email = "https://localhost/lumen-jwt/public/api/auth/forgot_password/".$user->secretcode."";
            $this->sendEmail($email, $subject, $compose_email);

            $ket = $this->responsejson('Email Berhasil Terkirim', 200, true, $user->username);

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }  

        return response()->json($ket);
    }

    public function forgot_password(Request $r, $secretcode)
    {
        //inputannya secretcode dan password
        $input = $r->only([
            'password',
            'repeat_password',
        ]);

        $input['secretcode'] = $secretcode;

        $validator = Validator::make($input,[
                
            'secretcode'      => 'required',
            'password'        => 'required|min:6|max:50', 
            'repeat_password' => 'required|min:6|max:50|same:password',

        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
        
           $user_count = Users::where('secretcode', $input['secretcode'])->count();
            //dd($user_count);
            if ($user_count == 0){
                $validator->errors()->add('secretcode', 'Secretcode Yang Anda Masukan SALAH Atau Invalid');
            }

        });

        if( $validator->fails() == false ){

            $user = Users::where('secretcode', $secretcode)->first();
            $user->password = Hash::make($r->input('password'));
            $user->save();

            $ket = $this->responsejson('Password Anda Telah Di Ubah', 200, true, $user->username);

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        } 

        return response()->json($ket);
    }

    public function logout()
    {

    	Auth::logout();
    	JWTAuth::invalidate(JWTAuth::getToken());
   
        $ket = $this->responsejson([], 200, true);
        return response()->json($ket);

    }

    public function request_phone(Request $r)
    {
        $input = $r->only([
            'username',
        ]);

        $validator = Validator::make($input,[
                
            'username' => 'required',

        ]);

        if( $validator->fails() == false ){
            //mencari data di table users.
            $user = Users::where('username', $r->input('username'))->first();
            //1. mengirim SMS ke usernya.
            $nohp  = $user->phone;
            $pesan = 'Code PIN Anda Adalah '.$user->pin;
            $this->sendSMS($nohp, $pesan);

            //dd('Code PIN Anda Adalah '.$user->pin);
            $ket = $this->responsejson('SMS Berhasil Terkirim', 200, true, $user->username);

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }  

        return response()->json($ket);

    }

    public function verify_phone(Request $r)
    {
        $input = $r->only([
            'username',
            'pin',
        ]);

        $validator = Validator::make($input,[
                
            'username' => 'required',
            'pin'      => 'required|numeric',

        ]);
        

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
        
            $user = Users::where('username', $input['username'])->first();

            if ($input['pin'] != $user->pin){
                $validator->errors()->add('pin', 'PIN Yang Anda Masukan SALAH');
            }

        });

        if( $validator->fails() == false ){

            $user = Users::where('username', $r->input('username'))->first();
            $user->status = 1;
            $user->save();

            $ket = $this->responsejson('User Berhasil Terverifikasi', 200, true, $user->username);

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        } 

        return response()->json($ket);

    }

    public function request_email(Request $r)
    {
        $input = $r->only([
            'username',
        ]);

        $validator = Validator::make($input,[
                
            'username' => 'required',

        ]);

        if( $validator->fails() == false ){
            //mencari data di table users.
            $user = Users::where('username', $r->input('username'))->first();
            //1. mengirim Email ke usernya.
            $email  = $user->email;
            $subject = 'Confirm '.$user->email.' on Rentcar';
            $compose_email = "https://localhost/lumen-jwt/public/api/auth/verify_email/".$user->secretcode."";
            $this->sendEmail($email, $subject, $compose_email);

            //dd($compose_email);
            $ket = $this->responsejson('Email Berhasil Terkirim', 200, true, $user->username);

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json($ket);

    }

    public function verify_email($secretcode)
    {
        $input = [
            'secretcode' => $secretcode
        ];
        $validator = Validator::make($input,[

            'secretcode' => 'required',

        ]);
        
        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
        
            $user_count = Users::where('secretcode', $input['secretcode'])->count();
            //dd($user_count);
            if ($user_count == 0){
                $validator->errors()->add('secretcode', 'Secretcode Yang Anda Masukan SALAH Atau Invalid');
            }

        });

        if( $validator->fails() == false ){

            $user = Users::where('secretcode', $secretcode)->first();
            $user->status = 1;
            //$user->save();
            if ($user->save()) {
                $this->generatedSecretcode();
            }

            $ket = $this->responsejson('User Berhasil Terverifikasi', 200, true, $user->username);

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        } 

        return response()->json($ket);
    }

    public function postRegister(Request $request)
    {

        $input = $request->only([
            'name',
            'username',
            'email',
            'password',
            'password_confirmation',
            'roles',
            'phone',
        ]);

        $validator = Validator::make($input, [
            'name' => 'required|max:255',
            'username' => 'required|min:5|max:15|unique:users',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|confirmed|min:6',
            'roles' => 'required',
            'phone' => 'required|numeric',
        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {

            $role_count = User_roles::where('name', $input['roles'])->count();
            if ($role_count == 0) {
                $validator->errors()->add('roles', 'Rolesnya Tidak Ada');
            }

        });
        // END VALIDATION

        if( $validator->fails() == false ){

            $user = new Users;
            $user->name = $request->name;
            $user->roles = $request->roles;
            $user->phone = $request->phone;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->register_time = date('Y-m-d H:i:s');
            $user->status = 0;
            $user->secretcode = $this->generatedSecretcode();
            $user->pin = rand(1000,9999);

            if($user->save() == true)
            {

                $ket = $this->responsejson('Berhasil Terdaftar', 200, true, $user->username);

            }else{

                $ket = $this->responsejson('Tidak Berhasil Terdaftar', 200, false);

            }

        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        } 

        return response()->json($ket);
    }

    public function uploadImage(Request $request)
    {
        // vlidasi
        // ekstensi : jpg, png, jpeg
        // max size 2 mb

        $file = $request->file('image');

        $input = [];
        $input['image'] = $file;

        $validator = Validator::make($input,[   
            'image' => 'required|max:2068|mimes:jpg,jpeg,png',
        ]);

        $ext = $file->getClientOriginalExtension();

        // validator
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
           
        });
    
        // proses inti
        if( $validator->fails() == false ){
            $newName = rand(100000,1001238912).".".$ext;
            $success = $file->move('image',$newName);
            if($success){
                $user = Users::where('username', Auth::user()->username)->first();
                //dd($user->image);
                //delete file sebelumnya
                if ($user->image != "") {
                    unlink(__DIR__.'/../../../public/image/'. $user->image);
                }
                $user->image = $newName;
                $user->save();

                $ket = $this->responsejson('Image Berhasil Di Upload', 200, true, $user->username);
            }else{
                $ket = $this->responsejson('Image Gagal Di Upload', 200, false);
            }
        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json($ket);
    }

}
