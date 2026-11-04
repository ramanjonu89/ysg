<?php

namespace App\Handler;

use App\Handler\Filter\BrandFilter;
use App\Handler\Filter\CategoryFilter;
use App\Handler\Filter\CategoryGroupFilter;
use App\Handler\Filter\FilterInterface;
use App\Handler\Filter\KeywordFilter;
use App\Handler\Filter\SubCategoryFilter;
use App\Handler\Filter\SubsubCategoryFilter;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductFilterHandler
{
    protected $params = [];
    protected $conditions = [];
    protected $products;
    protected $request;
    protected $filterItems = [];
    protected $query;
    protected $paginate = 12;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->prepareQuery();
    }

    public function prepareQuery()
    {
        $this->query = Db::table('products as p');

        $this->query->leftJoin('users as u', 'p.user_id', '=', 'u.id');
        $this->query->leftJoin('sellers as s', 'u.id', '=', 's.user_id');
        $this->query->leftJoin('brands as b', 'p.brand_id', '=', 'b.id');
        $this->query->leftJoin('categories as c', 'p.category_id', '=', 'c.id');
        $this->query->leftJoin('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id');
        $this->query->leftJoin('sub_sub_categories as ssc', 'p.subsubcategory_id', '=', 'ssc.id');

        $this->query->where('p.published', '=', 1)
            ->where('s.verification_status', '=', Seller::VERIFICATION_STATUS_ENABLED)
            ->where('u.company_type', '=', User::COMPANY_TYPE_FLAG_SHIP)
            ;

        if (!isMicrosite()) {
            $micrositeUserIds = DB::table('microsite_users')->pluck('user_id')->toArray();
            $this->query->where('p.only_microsite', Product::ONLY_MICROSITE)
                ->whereNotIn('u.id', $micrositeUserIds);
            ;
        }

        $this->query->select('p.slug', 'p.id', 'p.discount', 'p.name', 'p.thumbnail_img', 'p.rating', 'b.name as brandName');

    }

    /**
     * @throws BindingResolutionException
     */
    public function execute()
    {
        $this->addFilter('keyword', app()->make(KeywordFilter::class));
        $this->addFilter('categoryGroup', app()->make(CategoryGroupFilter::class));
        $this->addFilter('brand', app()->make(BrandFilter::class));
        return $this->getResults();
    }

    public function setPaginate(int $number): self
    {
        $this->paginate = $number;

        return $this;
    }

    public function addFilter(string $name, FilterInterface $filter): self
    {
        $this->filterItems[$name] = $filter;

        return $this;
    }

    public function getFilter(string $name)
    {
        return $this->filterItems[$name] ?? null;
    }

    public function getFilters(): array
    {
        return $this->filterItems;
    }

    public function getResults()
    {
        foreach ($this->filterItems as $filter) {
            $filter->buildQuery($this->query);
        }

        return $this->query->paginate($this->paginate);
    }
}