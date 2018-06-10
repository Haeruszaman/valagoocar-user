<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_car;
use App\Transformer\M_carTransformer;
use Validator;
use Auth;

class M_carController extends RentcarController
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
        
        $model = new M_car;

        $queries = [];

        $columns_filter = ['filter_merk' => "merk"];

        // FILTER
        foreach ($columns_filter as $key => $column) {
            if ($request->has($key)) {
                $model = $model->where($column, $request->$key);
                $queries[$key] = $request->$key;
            }
        }
        
        // SEARCHING
        if ($request->has('keyword') == true) {
         
            $model = $model->Where("name", "like","%{$request->input('keyword')}%")
                  ->orWhere("merk", "like","%{$request->input('keyword')}%")
                  ->orWhere("year", "like","%{$request->input('keyword')}%");
            $queries['keyword'] = $request->keyword;
        }

        // SORTING
        if ($request->has('sort') == true) {
            if ($request->input('sort') == 'TAHUN_TERBARU' ) {
                $model = $model->orderBy('year', 'DESC');
            }elseif ($request->input('sort') == 'TAHUN_TERLAMA' ) {
                $model = $model->orderBy('year', 'ASC');
            }
            $queries['sort'] = $request->sort;
        }
        
        // PAGING
        $result = $model->paginate(10)->appends($queries);
 
        return $this->response->paginator($result, new M_carTransformer);
        
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
            $M_car_count = M_car::where('id',  $input['id'])->count(); 
            if ($M_car_count == 0) {
                $validator->errors()->add('id', 'ID Yang Anda Inputkan Salah Atau Datanya Tidak Ditemukan');
            }
            
        });

        //End Validate
        if( $validator->fails() == false ){
            $model = M_car::findOrFail($id);
            return (new M_carTransformer)->transform($model);
        }else{
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
    }

}