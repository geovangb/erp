<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->safeEmail,
            'customer_phone' => $this->faker->phoneNumber,
            'customer_cep' => '12345-678',
            'customer_address' => $this->faker->address,
            'freight' => 10.0,
            'payment_method' => 'pix',
            'user_id' => 1, // ou factory de User se quiser associar
            'subtotal' => 100.0,
            'total' => 110.0,
            'status' => 'pendente',
        ];
    }
}
