<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class adminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'user_role_id' => "3",
            'name' => "vinita",
            'email' => "vinita.kataria3333@gmail.com",
            'phone_number' => "9929184517",
            'is_active' => "1",
            'password' => Hash::make('Vinita@3333'),
        ]);
    }
}
