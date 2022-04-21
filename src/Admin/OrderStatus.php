<?php

declare(strict_types=1);

namespace App\Admin;

abstract class OrderStatus
{
    public const status = [
        'PENDING' => 'Pending',
        'DELIVERED' => 'Delivered',
        'CANCELLED' => 'Cancelled',
        'RETURNED' => 'Returned'
    ];
}