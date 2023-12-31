<?php
// database/factories/MessageFactory.php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'sender' => $this->faker->numberBetween(1, 10),
            'reciver' => $this->faker->numberBetween(1, 10),
            'message' => $this->faker->text(200),
            'subject' => $this->faker->text(45),
            'recieved_flag' => $this->faker->numberBetween(0,1), // Randomly generates 0 or 1
        ];
    }
}
