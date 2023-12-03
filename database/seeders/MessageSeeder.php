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
        $receiver = User::inRandomOrder()->first();

        // Check if both sender and receiver exist
        if ($sender && $receiver) {
            // Create a single message
            Message::create([
                'sender' => $sender->id,
                'receiver' => $receiver->id,
                'message' => 'Example message content.',
                'subject' => 'Example Subject',
                'received_flag' => 1, // Randomly generates 0 or 1
            ]);
        }

        // Create multiple messages using factory
        Message::factory(500)->create();
    }
}
