<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User; // Assuming User model is in the same namespace

class MessageSeeder extends Seeder
{
    public function run() : void
    {
        // Create a single message
        Message::create([
            'sender' => User::inRandomOrder()->first()->id,
            'reciver' => User::inRandomOrder()->first()->id,
            'message' => 'Example message content.',
            'subject' => 'Example Subject',
            'recieved_flag' => 1, // Randomly generates 0 or 1
        ]);

        // Create multiple messages using factory
        Message::factory(30)->create();
    }
}
