<?php

namespace App\Modules;

use Attribute;

#[\Attribute(Attribute::TARGET_CLASS)]
class AsModule
{
    public function __construct(public readonly string $id, public readonly int $expirationInterval)
    {

    }
}