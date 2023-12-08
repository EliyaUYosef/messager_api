<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User; // Assuming User model is in the same namespace

class MessageSeeder extends Seeder
{
    public function run() : void
    {
        // Get a random sender and receiver
        $sender = User::inRandomOrder()->first();
        $reciver = User::inRandomOrder()->first();

        // Check if both sender and receiver exist
        if ($sender && $reciver) {
            // Create a single message
            Message::create([
                'sender' => $sender->id,
                'reciver' => $reciver->id,
                'message' => 'Example message content.',
                'subject' => 'Example Subject',
                'recieved_flag' => 1, // Randomly generates 0 or 1
            ]);
        }

        // Create multiple messages using factory
        Message::factory(500)->create();
    }
}
