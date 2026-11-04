<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTask extends Model
{
    const DAILY_FREQUENCY = 'daily';
    const WEEKLY_FREQUENCY = 'weekly';
    const MONTHLY_FREQUENCY = 'monthly';

    const FREQUENCY_LIST = [
        self::DAILY_FREQUENCY => 'Daily',
        self::WEEKLY_FREQUENCY => 'Weekly',
        self::MONTHLY_FREQUENCY => 'Monthly',
    ];

    const DEFAULT_CRON = [
        self::DAILY_FREQUENCY => [
            'minute' => '00',
            'hour' => '00',
            'day_month' => '*',
            'month' => '*',
            'day_week' => '*',
        ],
        self::WEEKLY_FREQUENCY => [
            'minute' => '00',
            'hour' => '00',
            'day_month' => '*',
            'month' => '*',
            'day_week' => '0',
        ],
        self::MONTHLY_FREQUENCY => [
            'minute' => '00',
            'hour' => '00',
            'day_month' => '1',
            'month' => '*',
            'day_week' => '*',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data', 'type', 'frequency',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCronValueAttribute()
    {
        $cron = self::DEFAULT_CRON[$this->frequency];

        if (null !== $this->options && $this->frequency === self::DAILY_FREQUENCY) {
            $segments = explode(':', $this->options);
            $hour = $segments[0];
            $minute = $segments[1];

            $cron['minute'] = trim($minute);
            $cron['hour'] = trim($hour);
        }

        if (null !== $this->options && $this->frequency === self::WEEKLY_FREQUENCY) {
            $segments = explode(',', $this->options);

            $day = $segments[0];
            $time = $segments[1];

            $cron['day_week'] = date('N', strtotime($day));

            $segments = explode(':', $time);
            $hour = $segments[0];
            $minute = $segments[1];

            $cron['minute'] = trim($minute);
            $cron['hour'] = trim($hour);
        }

        return implode(' ', $cron);
    }
}
