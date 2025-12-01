<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'General',
                'slug' => 'general',
                'description' => 'Gneral topics',
                'icon' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'History',
                'slug' => 'history',
                'description' => 'History of Libya',
                'icon' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Economy',
                'slug' => 'economy',
                'description' => 'Some info about the economy',
                'icon' => NULL,
                'created_at' => NULL,
                'updated_at' => '2025-12-01 10:41:08',
            ),
        ));
        
        
    }
}