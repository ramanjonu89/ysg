<?php

namespace App\Excel\ProductImport;

use DateTime;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class Repo
 * @package App\Excel\ProductImport
 * @property
 */
class RuleDateComparison implements Rule
{
    protected $startDate;
    protected $maxQty;

    public function __construct($startDate = null, $maxQty)
    {
        $this->startDate = $startDate;
        $this->maxQty    = $maxQty;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($this->maxQty)) {
            return true;
        }

        $startDate = new DateTime($this->startDate);
        $endDate   = new DateTime($value);

        return $endDate >= $startDate;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a date greater than or equal to ' . $this->startDate . '.';
    }
}