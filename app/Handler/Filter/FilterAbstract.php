<?php

namespace App\Handler\Filter;

use Exception;
use Illuminate\Http\Request;

class FilterAbstract implements FilterInterface
{
    protected $value = '';
    protected $options =null;
    protected $request;
    protected $field;

    /**
     * @throws Exception
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->initField();
        if ($this->isApplied()) {
            $this->value = $this->request->{$this->field};
        }
    }

    /**
     * @throws Exception
     */
    public function initField()
    {
        throw new Exception(get_class($this) . ' need implement initField method');
    }

    public function isApplied(): bool
    {
        return !is_null($this->request->{$this->field});
    }

    /**
     * @throws Exception
     */
    public function buildQuery($query)
    {
        throw new Exception(get_class($this) . ' need implement buildQuery method');
    }
}