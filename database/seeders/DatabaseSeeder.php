<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'hamza ben romdhane',
            'email' => 'hamza.ben.romdhane98@gmail.com',
            'password' => Hash::make("hamza12321",[
                'rounds' =>12
            ]),
            'role' => 'admin'
        ]);

        DB::table('menus')->insert([
            'name' => 'pizza',
            'price' => '12.00'
        ]); 
        DB::table('menus')->insert([
            'name' => 'chapatti',
            'price' => '3.00'
        ]); 

        DB::table('ingredients')->insert([
            'name' => 'salami',
            'price' => '1.0'
        ]);
        DB::table('ingredients')->insert([
            'name' => 'jambon',
            'price' => '1.5'
        ]);
        DB::table('ingredients')->insert([
            'name' => 'thon',
            'price' => '0.5'
        ]);
        DB::table('ingredient_menu')->insert([
            'ingredient_id' => 1,
            'menu_id' => 1
        ]);
        DB::table('ingredient_menu')->insert([
            'ingredient_id' => 2,
            'menu_id' => 1
        ]);
        DB::table('ingredient_menu')->insert([
            'ingredient_id' => 3,
            'menu_id' => 1
        ]);
        DB::table('ingredient_menu')->insert([
            'ingredient_id' => 3,
            'menu_id' => 2
        ]);
        DB::table('ingredient_menu')->insert([
            'ingredient_id' => 1,
            'menu_id' => 2
        ]);
        DB::table('coupons')->insert([
            'name'=>'berlom',
            'discount'=>0.2
        ]);
        DB::table('paniers')->insert([
            'user_id'=>1,
            'menu_id'=>2,
            'ingredients' => '1@3',
            'quantity' => 5
        ]);
    }
}
