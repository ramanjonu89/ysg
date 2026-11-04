<?php
namespace App\Utility;

class DateTimeHelper
{
    public static function getDurationString(int $seconds)
    {
        // $days = floor($seconds / 86400);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds - (
                ($hours * 3600) +
                ($minutes * 60)
            );

        return "{$hours} hrs {$minutes} mins {$seconds} sec";
    }
}