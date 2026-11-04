<?php

namespace App\Services;

use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use Illuminate\Http\Request;
use App\Services\AbstractService;

class AttributeService extends AbstractService
{
    public function __construct(AttributeRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getAttribute()
    {
       return $this->repo->getAttribute();
    }
}
