<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\NewSletter;
use App\Services\NewSletterService;

class NewsletterDateRange implements Rule
{
    /**
     * Ignore current record when updating
     *
     * @var int|null
     */
    protected $ignoreId;
    protected $service;
    /**
     * Create a new rule instance.
     *
     * @param int|null $ignoreId
     */
    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
        $this->service = app(NewSletterService::class);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!request('status')) {
            return true;
        }

        return !$this->service->hasConflict(
            request('start_date'),
            request('end_date'),
            $this->ignoreId
        );

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Selected Date/Time Overlap with Existing Promotion Banner';
    }
}
