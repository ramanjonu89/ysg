<?php
namespace App\Repositories;

use App\Models\Page;

class PageRepository extends AbstractRepo
{

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Page::class;
    }

}
