<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait RecordSignature
{
    static $record = true;

    protected static function bootRecordSignature()
    {
        if (Auth::check()) {
            static::creating(function ($model) {
                $model->created_by = Auth::user()->id;
            });

            static::updating(function ($model) {
                $model->updated_by = Auth::user()->id;
            });

            static::deleting(function ($model) {
                $model->deleted_by = Auth::user()->id;
                $model->saveQuietly();
            });
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::bootRecordSignature();
    }
}
