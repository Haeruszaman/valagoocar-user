<?php
namespace App\Transformer;
use App\Models\Service;
use League\Fractal\TransformerAbstract;

class ServiceTransformer extends TransformerAbstract
{
    public function transform(Service $Service) {

        return [
            'code' => $Service->code,
            'vendor' => [
                'username' => $Service->vendor,
                'name' => $Service->userOne == null ? "" : $Service->userOne->name,
                'phone' => $Service->userOne == null ? "" : $Service->userOne->phone,
                'city' => $Service->userOne == null ? "" : $Service->userOne->city,
            ],
            'car' => [
                'name' => $Service->car,
                'merk' => $Service->carOne == null ? "" : $Service->carOne->merk,
                'seat' => $Service->carOne == null ? "" : $Service->carOne->seat,
                'loading' => $Service->carOne == null ? "" : $Service->carOne->loading,
                'year' => $Service->carOne == null ? "" : $Service->carOne->year,
            ],
            'description' => $Service->description,
            'city' => $Service->city,
            'price' => $Service->price,
            'is_active' => [
                'code' => $Service->is_active,
                'name' => $Service->is_active == 1 ? "Active" : "Not Active",
            ],
        ];        
    }
}