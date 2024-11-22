<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      User::create([
        'nombre_completo'=>'SuperAdmin',
        'email'=>'admin@test.com',
        'password'=>bcrypt('12345r'),
      ])->assignRole('Administrador');

      User::factory(9)->create();
    }
}
