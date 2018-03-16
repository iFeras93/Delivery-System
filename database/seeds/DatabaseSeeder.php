<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('users')->insert([
            'name' => 'admin system',
            'email' => 'admin@admin.com',
            'password' => bcrypt(123456), // secret
            'type' => 'admin',]);

        DB::table('settings')->insert([
            'price_of_km' => 0.50,
            'main_email' => 'main@admin.com',
            'PAYPAL_SANDBOX_CLIENT_ID' => 'AfESydqlcKfCwSu7ot6pm3AXcP5WrfMYHh0Sc99BEotuhpISilw5DK0HdOtabN02EfV397vkVSlhrRd8',
            'PAYPAL_SANDBOX_SECRET' => 'EB4h4QzWLVSciKCclW1D50hzT0WFzM0F3M6HTdMACpbsdpuAa1KbEbmA0pa8GWR0JS7HWlkFSQRgfIfe',
            'main_long' => 34.2480005,
            'main_lat' => 31.2947692,
        ]);
    }
}
