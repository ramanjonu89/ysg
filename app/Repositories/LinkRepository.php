<?php
namespace App\Repositories;

use App\Models\Link;

class LinkRepository extends AbstractRepo
{
    public function getModel()
    {
        return Link::class;
    }
}