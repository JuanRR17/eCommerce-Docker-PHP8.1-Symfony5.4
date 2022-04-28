<?php

declare(strict_types=1);

namespace App\Exception\Product;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductNotFoundException extends NotFoundHttpException
{
    public static function fromProductId($id): self
    {
        throw new self(\sprintf('Product with id %s not found', $id));
    }
}

