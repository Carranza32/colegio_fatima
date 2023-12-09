<?php

namespace App\Traits;

use App\Scopes\ActiveScope;

trait ActiveScopeTrait
{
    protected static function bootActiveScopeTrait()
    {
        if (tenancy()->tenant) {
            static::addGlobalScope(new ActiveScope);
        }
    }
}
