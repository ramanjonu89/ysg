<?php

namespace App\Handler\Filter;

use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryGroupFilter extends FilterGroupAbstract
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->addFilter('category', app()->make(CategoryFilter::class));
        $this->addFilter('subcategory', app()->make(SubCategoryFilter::class));
        $this->addFilter('subsubcategory', app()->make(SubsubCategoryFilter::class));
    }

    public function render()
    {
        $mainCategories = app()->make(CategoryService::class)->getMainCategories();
        return view('frontend.partials.search.items.categoryGroup', compact('mainCategories'));
    }
}