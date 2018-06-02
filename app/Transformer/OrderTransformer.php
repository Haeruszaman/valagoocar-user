<?php
namespace App\Transformer;
use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $Order) {
        $statusname = "";
        if($Order->status == 1) $statusname = "Request";
        else if($Order->status == 2) $statusname = "Paid Off";
        else if($Order->status == -1) $statusname = "Rejected";

        return [
            'code' => $Order->code,
            'service' => [
                'code' => $Order->service_code,
                'vendor' => $Order->serviceOne == null ? "" : $Order->serviceOne->vendor
            ],
            'customer' => [
                'username' => $Order->user,
                'name' => $Order->userOne == null ? "" : $Order->userOne->name,
                'phone' => $Order->userOne == null ? "" : $Order->userOne->phone,
                'city' => $Order->userOne == null ? "" : $Order->userOne->city,
            ],
            'order' => [
                'date' => date('d-m-Y', strtotime($Order->order_date)),
                'time' => $Order->order_time,
                'end' => date('d-m-Y', strtotime($Order->end_date)),
            ],
            'days' => $Order->days,
            'address_order' => $Order->address_order,
            'city' => $Order->city,
            'car' => $Order->car,
            'description' => $Order->description,
            'price_total' => $Order->price_total,
            'status' => [
                'code' => $Order->status,
                'name' => $statusname
            ],
        ];
    }
}