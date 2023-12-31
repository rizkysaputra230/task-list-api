<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->name . '@gmail.com',
                'phone' => $faker->phoneNumber,
                'password' => Hash::make('password'),
                'upload_photo' => Str::random(20),
                'upload_cv' => Str::random(20)
            ]);
        };
    }
}
