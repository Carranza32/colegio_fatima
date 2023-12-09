<?php

namespace App\Traits;

trait RecordSignature
{
    static $record = true;

    protected static function boot()
    {
        parent::boot();

        if (auth()->user()) {
            static::creating(function ($model) {
                if (config('recordsignature.enable')) {
                    $model->created_by = auth()->user()->id;
                }
            });

            static::updating(function ($model) {
                if (config('recordsignature.enable')) {
                    $model->updated_by = auth()->user()->id;
                }
            });

            static::deleting(function ($model) {
                if (config('recordsignature.enable')) {
                    $model->deleted_by = auth()->user()->id;
                    $model->saveQuietly();
                }
            });
        }
    }


}
