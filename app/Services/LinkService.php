<?php

namespace App\Services;

use App\Repositories\LinkRepository;
use App\Services\AbstractService;

class LinkService extends AbstractService
{
    public function __construct(LinkRepository $repo)
    {
        parent::__construct($repo);
    }
}
