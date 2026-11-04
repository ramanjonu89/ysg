<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewSletter extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const START_DATE_ONLY = 'start_date_only';
    const END_DATE_ONLY   = 'end_date_only';
    const DATE_RANGE      = 'date_range';

    protected $table = 'newsletter_config';

    protected $fillable = [
        'photo',
        'description',
        'status',
        'show_subbox',
        'url',
        'start_date',
        'end_date'
    ];

    public function getStartDatetimeAttribute()
    {
        return $this->start_date ? date('d/m/Y H:i', $this->start_date) : '';
    }

    public function getEndDatetimeAttribute()
    {
        return $this->end_date ? date('d/m/Y H:i', $this->end_date) : '';
    }

    public function isExpired() {
        $now = strtotime(date('d-m-Y H:i:s'));

        if (empty($this->start_date) && empty($this->end_date)) {
            return false;
        }

        if (!empty($this->start_date) && !empty($this->end_date)) {
            return  $now < $this->start_date ||
                    $now > $this->end_date;

        } elseif (!empty($this->start_date)) {
            return $now <= $this->start_date;

        } elseif (!empty($this->end_date)) {
            return $now >= $this->end_date;
        }

        return false;
    }
}
