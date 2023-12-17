<?php
// app/Traits/Auditable.php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::creating(function ($model) {
            dd(Auth::user());
            $model->created_by = Auth::user()->id ?? null;
        });

        static::updating(function ($model) {
            dd(Auth::user());
            $model->updated_by = Auth::user()->id ?? null;
        });

        static::deleting(function ($model) {
            $model->deleted_by = Auth::user()->id ?? null;
            $model->saveQuietly();
        });
    }
}
