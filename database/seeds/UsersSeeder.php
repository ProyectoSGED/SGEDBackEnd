<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i < 12; $i++) {
            DB::table('users')->insert([
                'name' => $faker->userName,
                'password' => \Illuminate\Support\Facades\Hash::make('1234'),
                'email' => $faker->email,
				'created_at' => date("Y-m-d H:i:s"),
        		'updated_at' => date("Y-m-d H:i:s")
 			]);
        }
    }
}
