<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Transformer\ServiceTransformer;
use Validator;
use Auth;

class ServiceController extends RentcarController
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
        
        $model = new Service;
        $model = $model->where('vendor', Auth::user()->username);
        $queries = [];

        $columns_filter = ['is_active' => "is_active"];

        // FILTER
        foreach ($columns_filter as $key => $column) {
            if ($request->has($key)) {
                $model = $model->where($column, $request->$key);
                $queries[$key] = $request->$key;
            }
        }
        
        // SEARCHING
        if ($request->has('keyword') == true) {
         
            $model = $model->Where("car", "like","%{$request->input('keyword')}%")
                  ->orWhere("description", "like","%{$request->input('keyword')}%")
                  ->orWhere("city", "like","%{$request->input('keyword')}%");
            $queries['keyword'] = $request->keyword;
        }

        // SORTING
        if ($request->has('sort') == true) {
            if ($request->input('sort') == 'HARGA_TERTINGGI' ) {
                $model = $model->orderBy('price', 'DESC');
            }elseif ($request->input('sort') == 'HARGA_TERENDAH' ) {
                $model = $model->orderBy('price', 'ASC');
            }
            $queries['sort'] = $request->sort;
        }
        
        // PAGING
        $result = $model->paginate(10)->appends($queries);
 
        return $this->response->paginator($result, new ServiceTransformer);
        
    }

    public function show($code)
    {
        $input = [];
        $input['code'] = $code;

        $validator = Validator::make($input,[   
            'code'         => 'required',
        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {

            //validasi 1. pengecekan datanya
            $Service_count = Service::where('code',  $input['code'])->count(); 
            if ($Service_count == 0) {
                $validator->errors()->add('code', 'Code Yang Anda Inputkan Salah Atau Datanya Tidak Ditemukan');
            }
            
        });

        //End Validate
        if( $validator->fails() == false ){
            $model = Service::where('code', $code)->first();
            return (new ServiceTransformer)->transform($model);
        }else{
            $m = $this->displayerors($validator);
            return $this->responsejson($m, 200, false);
        }
    
    }
}