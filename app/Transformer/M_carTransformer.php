<?php
namespace App\Transformer;
use App\Models\M_car;
use League\Fractal\TransformerAbstract;

class M_carTransformer extends TransformerAbstract
{
    public function transform(M_car $M_car) {

        return [
            'name' => $M_car->name,
            'merk' => $M_car->merk,
            'seat' => $M_car->seat,
            'loading' => $M_car->loading,
            'year' => $M_car->year,
        ];  
    }
}