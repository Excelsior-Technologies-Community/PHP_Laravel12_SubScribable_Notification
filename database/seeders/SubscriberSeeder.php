<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscriber;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {
        Subscriber::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Subscriber::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);
    }
}