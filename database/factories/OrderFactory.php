<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuses = OrderStatus::pluck('uuid', 'title')->toArray();
        $status = Arr::random(array_keys($statuses));

        $payments = null;
        if ($status == 'shipped' || $status == 'paid') {
            $payments = Payment::pluck('uuid')->toArray();
        }
        $payment = $payments ? Arr::random($payments) : '';

        $user = User::where('is_admin', 0)->pluck('uuid')->toArray();
        $user = Arr::random($user);

        $products = Product::pluck('price', 'uuid')->toArray();

        // dd($products);
        $nbProds = rand(5,15);
        $items = [];
        $sum = [];
        for ($i=0; $i < $nbProds; $i++) { 
            $prod = Arr::random(array_keys($products));
            $quant = rand(1,10);
            array_push($items, ['uuid'=> $prod, 'quantity' => $quant]);
            array_push($sum, floatVal($products[$prod]) * floatVal($quant));
        }
        return [
            'uuid' => $this->faker->uuid(),
            'user_id' => $user,
            'order_status_id' => $statuses[$status],
            'payment_id' => $payment,
            'products' => $items,
            'address' => ['billing' => $this->faker->address(),'shipping' => $this->faker->address()],
            'amount' => array_sum($sum),
            'delivery_fee' => array_sum($sum) > 500 ? 0 : 15,
            'shipped_at' => $status == 'shipped' ? now() : null
        ];
    }
}
