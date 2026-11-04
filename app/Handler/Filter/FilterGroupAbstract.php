<?php

namespace App\Handler\Filter;

use Exception;
use Illuminate\Http\Request;

class FilterGroupAbstract implements FilterInterface
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * @throws Exception
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function addFilter(string $name, FilterInterface $filter)
    {
        $this->filters[$name] = $filter;
    }

    public function isApplied(): bool
    {
        return true;
    }

    /**
     * @throws Exception
     */
    public function buildQuery($query)
    {
        foreach ($this->filters as $filter) {
            $filter->buildQuery($query);
        }
    }

    /**
     * @throws Exception
     */
    public function render()
    {
        throw new Exception(get_class($this) . ' need implement render method');
    }
}