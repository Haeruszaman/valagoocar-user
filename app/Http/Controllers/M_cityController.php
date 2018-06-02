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

        $queries = [];

        /*$columns_filter = ['filter_merk' => "merk"];

        // FILTER
        foreach ($columns_filter as $key => $column) {
            if ($request->has($key)) {
                $model = $model->where($column, $request->$key);
                $queries[$key] = $request->$key;
            }
        }*/
        
        // SEARCHING
        if ($request->has('keyword') == true) {
         
            $model = $model->Where("name", "like","%{$request->input('keyword')}%");
            $queries['keyword'] = $request->keyword;
        }

        // SORTING
        /*if ($request->has('sort') == true) {
            if ($request->input('sort') == 'TAHUN_TERBARU' ) {
                $model = $model->orderBy('year', 'DESC');
            }elseif ($request->input('sort') == 'TAHUN_TERLAMA' ) {
                $model = $model->orderBy('year', 'ASC');
            }
            $queries['sort'] = $request->sort;
        }*/
        
        // PAGING
        $result = $model->paginate(10)->appends($queries);
 
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
            return $this->resnposejson($m, 200, false);
        }
    
    }

    public function store(Request $r)
    {
        $input = $r->only([
            'name',
        ]);

        $validator = Validator::make($input,[
            'name'    => 'required',
        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {

        });

        if( $validator->fails() == false ){
            
            $model = new M_city; 
            $model->name    = $r->input('name');
                    
            if($model->save() == true){
                $ket = $this->responsejson('Berhasil Dibuat', 200, true, $model->id);
            }else{
                $ket = $this->responsejson('Tidak Berhasil Dibuat', 200, false);
            }

        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
        }

        return response()->json($ket);
    
    }

    public function edit(Request $r, $id)
    {
        $input = $r->only([
            'name',
        ]);

        $validator = Validator::make($input,[
            'name'    => 'required',
        ]);

        // validasi proses bisnis
        $input['id'] = $id;
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            //validasi 1. pengecekan datanya
            $M_city_count = M_city::where('id',  $input['id'])->count(); 
            if ($M_city_count == 0) {
                $validator->errors()->add('id', 'ID Yang Anda Inputkan Salah Atau Datanya Tidak Ditemukan');
            }
        });

        // END VALIDATION
        if( $validator->fails() == false ){

            $model = M_city::where('id', $id)->firstOrFail();
            $model->name    = $r->input('name');
        
            if($model->save() == true){
                $ket = $this->responsejson('Berhasil Tersimpan', 200, true, $model->id);
            }else{
                $ket = $this->responsejson('Tidak Berhasil Tersimpan', 200, false);
            }

        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
        } 

        return response()->json($ket);

    }

    public function destroy($id)
    {
        $input = [];
        $input['id'] = $id;

        $validator = Validator::make($input,[    
            'id' => 'required', 
        ]);

        // validasi proses bisnis
        $input['id'] = $id;
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

            if($model->delete() == true){
                $ket = $this->responsejson('Berhasil Di Hapus', 200, true, $model->id);
            }else{
                $ket = $this->responsejson('Tidak Berhasil Di Hapus', 200, false);  
            }
    
        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
        }
        
        return response()->json($ket);
    
    }

}