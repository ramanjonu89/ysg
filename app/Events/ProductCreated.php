<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 22/05/2022
 * Time: 14:49
 */

namespace App\Events;

/**
 * Class ProductCreated
 * @package App\Events
 * @property
 */
class ProductCreated
{
    public $userId;
    public $productIds;

    /**
     * Create a new event instance.
     *
     * @param  array  $productIds
     */
    public function __construct($userId, array $productIds)
    {
        $this->userId = $userId;
        $this->productIds = $productIds;
    }
}