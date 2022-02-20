<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = new OrderStatus();
            $status->uuid = (string) Str::uuid();
            $status->title = 'paid';
        $status->save();

        $status = new OrderStatus();
            $status->uuid = (string) Str::uuid();
            $status->title = 'open';
        $status->save();

        $status = new OrderStatus();
            $status->uuid = (string) Str::uuid();
            $status->title = 'pending payment';
        $status->save();

        $status = new OrderStatus();
            $status->uuid = (string) Str::uuid();
            $status->title = 'shipped';
        $status->save();

        $status = new OrderStatus();
            $status->uuid = (string) Str::uuid();
            $status->title = 'cancelled';
        $status->save();
    }
}
