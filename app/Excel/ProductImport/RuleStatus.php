<?php

namespace App\Excel\ProductImport;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Repo
 * @package App\Excel\ProductImport
 * @property
 */
class RuleStatus implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $status = ucfirst(strtolower($value));
        $publishedStatus = Repo::STATUS;
        return isset($publishedStatus[$status]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $status = collect(Repo::STATUS)->keys()->implode(', ');

        return 'The :attribute must be one of the following types: '.$status;
    }
}