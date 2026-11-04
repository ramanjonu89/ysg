<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 02/09/2022
 * Time: 14:19
 */

namespace App\Repositories;

use App\Models\Currency;

/**
 * Class CurrencyRepository
 * @package App\Repositories
 * @property
 */
class CurrencyRepository extends AbstractRepo
{

    public function getModel(): string
    {
        return Currency::class;
    }
}