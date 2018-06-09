<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_city;
use App\Transformer\M_cityTransformer;
use Validator;
use Auth;

class M_cityController extends RentcarController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request)                 
    {
        
        $model = new M_city;
        
        // SEARCHING
        if ($request->has('keyword') == true) {
         
            $model = $model->Where("name", "like","%{$request->input('keyword')}%");
            $queries['keyword'] = $request->keyword;
        }
        
        // PAGING
        $result = $model->paginate(10);
 
        return $this->response->paginator($result, new M_cityTransformer);
        
    }

    public function show($id)
    {
        $input = [];
        $input['id'] = $id;

        $validator = Validator::make($input,[   
            'id'         => 'required',
        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {

            //validasi 1. pengecekan datanya
            $M_city_count = M_city::where('id',  $input['id'])->count(); 
            if ($M_city_count == 0) {
                $validator->errors()->add('id', 'ID Yang Anda Inputkan Salah Atau Datanya Tidak Ditemukan');
            }
            
        });

        //End Validate
        if( $validator->fails() == false ){
            $model = M_city::findOrFail($id);
            return (new M_cityTransformer)->transform($model);
        }else{
            $m = $this->displayerors($validator);
            return $this->responsejson($m, 200, false);
        }
    
    }

}