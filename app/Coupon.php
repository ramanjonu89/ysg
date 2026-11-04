<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1)
 * @method static select(string $string, string $string1)
 */
class Coupon extends Model
{
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_AMOUNT = 'amount';
}
