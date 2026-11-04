<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->truncate();
        $languages = [
            ['name' => 'English','code' => 'en','rtl' => 0, 'created_at' => date('Y-m-d h:i:s')],
            ['name' => 'Philippines','code' => 'ph','rtl' => 0,'created_at' => date('Y-m-d h:i:s')],
            ['name' => 'Vietnam','code' => 'vi','rtl' => 0,'created_at' => date('Y-m-d h:i:s')],
            ['name' => 'Malaysia','code' => 'ms','rtl' => 0,'created_at' => date('Y-m-d h:i:s')],
        ];
        DB::table('languages')->insert($languages);

    }
}
