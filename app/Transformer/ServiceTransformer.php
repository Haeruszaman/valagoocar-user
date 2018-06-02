<?php
namespace App\Transformer;
use App\Models\Service;
use League\Fractal\TransformerAbstract;

class ServiceTransformer extends TransformerAbstract
{
    public function transform(Service $Service) {

        return [
            'code' => $Service->code,
            'vendor' => $Service->vendor,
            'car' => $Service->car,
            'description' => $Service->description,
            'city' => $Service->city,
            'price' => $Service->price,
            'is_active' => $Service->is_active,
        ];        
    }
}