<?php
namespace App\Repositories;

use App\Models\Attribute;

class AttributeRepository extends AbstractRepo
{

    public function getModel()
    {
        return Attribute::class;
    }

    public function getAttribute()
    {
        return Attribute::all()->pluck('name', 'id')->toArray();
    }
  
}
