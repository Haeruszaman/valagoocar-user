<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;
use App\Transformer\OrderTransformer;
use Validator;
use Auth;

class OrderController extends RentcarController
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
        
        $model = new Order;

        $queries = [];

        $columns_filter = ['filter_status' => "status"];

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
                $model = $model->orderBy('price_total', 'DESC');
            }elseif ($request->input('sort') == 'HARGA_TERENDAH' ) {
                $model = $model->orderBy('price_total', 'ASC');
            }
            $queries['sort'] = $request->sort;
        }
        
        // PAGING
        $result = $model->paginate(10)->appends($queries);
 
        return $this->response->paginator($result, new OrderTransformer);
        
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
            $Order_count = Order::where('code',  $input['code'])->count(); 
            if ($Order_count == 0) {
                $validator->errors()->add('code', 'Code Yang Anda Inputkan Salah Atau Datanya Tidak Ditemukan');
            }
            
        });

        //End Validate
        if( $validator->fails() == false ){
            $model = Order::where('code', $code)->first();
            return (new OrderTransformer)->transform($model);
        }else{
            $m = $this->displayerors($validator);
            return $this->responsejson($m, 200, false);
        }
    
    }

    public function store(Request $r)
    {
        $input = $r->only([
            'service_code',
            'order_date',
            'order_time',
            'days',
            'address_order',
            'description',
        ]);

        $validator = Validator::make($input,[
            'service_code' => 'required',
            'order_date' => 'required',
            'order_time' => 'required',
            'days' => 'required',
            'address_order' => 'required',
            'description' => 'required',
        ]);

        // validasi proses bisnis
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            //validasi 1. pengecekan data service
            $service_count = Service::where('code',  $input['service_code'])->count(); 
            if ($service_count == 0) {
                $validator->errors()->add('service_code', 'Service Code Yang Anda Inputkan Salah Atau Datanya Tidak Ditemukan');
            }
        });

        if( $validator->fails() == false ){
            $service = Service::where('code', $r->input('service_code'))->first();
            
            $model = new Order;
            $model->code          = $this->generatedCodeOrder();
            $model->service_code  = $r->input('service_code');
            $model->order_time    = $r->input('order_time');
            $model->days          = $r->input('days');
            $model->description   = $r->input('description');
            $model->address_order = $r->input('address_order');
            $model->user          = Auth::user()->username;
            $model->order_date    = date('Y-m-d', strtotime($r->input('order_date')));
            $model->end_date      = date('Y-m-d', strtotime($r->input('days').' days', strtotime($r->input('order_date'))) -1);
            $model->price_total   = $r->input('days') * $service->price;
            $model->city          = $service->city;
            $model->car           = $service->car;
            $model->status        = 1;
                    
            if($model->save() == true){
                $ket = $this->responsejson('Berhasil Dibuat', 200, true, $model->code);
            }else{
                $ket = $this->responsejson('Tidak Berhasil Dibuat', 200, false);
            }

        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
        }

        return response()->json($ket);
    
    }

    /*public function updatestatus(Request $r, $code)
    {
        $input = $r->only([
            'status',
        ]);

        $validator = Validator::make($input,[
            'status'      => 'required|numeric',
        ]);
    
        // validasi proses bisnis
        $input['code'] = $code;
        $validator->setData($input);
        $validator->after(function($validator) use ($input) {
            //validasi 1. pengecekan data
            $order_count = Order::where('code',  $input['code'])->count(); 
            if ($order_count == 0) {
                $validator->errors()->add('code', 'Code Yang Anda Inputkan Salah Atau Datanya Tidak Ditemukan');
            }
        });

        // END VALIDATION
        if( $validator->fails() == false ){
            $model = Order::where('code', $code)->firstOrFail();
            $model->status  =  $r->input('status');
            
            if($model->save() == true){
                $ket = $this->responsejson('Berhasil Tersimpan', 200, true, $model->code);
            }else{
                $ket = $this->responsejson('Tidak Berhasil Tersimpan', 200, false);
            }

        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
        } 

        return response()->json($ket);
    }*/

    public function uploadImage(Request $request, $code)
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
            $success = $file->move('bukti',$newName);
            if($success){
                $order = Order::where('code', $code)->first();
                //dd($order);
                $order->image = $newName;
                $order->save();

                $ket = $this->responsejson('Image Berhasil Di Upload', 200, true, $order->code);
            }else{
                $ket = $this->responsejson('Image Gagal Di Upload', 200, false);
            }
        }else{
            $m = $this->displayerors($validator);
            $ket = $this->responsejson($m, 200, false);
        }

        return response()->json($ket);
    }

}