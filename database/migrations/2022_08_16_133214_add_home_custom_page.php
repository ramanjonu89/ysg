<?php

use App\Models\CustomPage\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHomeCustomPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $page = new Page();
        $page->title = 'Home Page';
        $page->slug = 'home-page';
        $page->type = Page::TYPE_HOMEPAGE;
        $page->is_enabled = 1;
        $page->need_login = 0;
        $page->exclusive_access = 0;

        $page->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Page::where('type', '=', Page::TYPE_HOMEPAGE)->delete();
    }
}
