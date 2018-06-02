<?php
namespace App\Transformer;
use App\Models\M_city;
use League\Fractal\TransformerAbstract;

class M_cityTransformer extends TransformerAbstract
{
    public function transform(M_city $M_city) {

        return [
            'name' => $M_city->name,
        ];  
    }
}