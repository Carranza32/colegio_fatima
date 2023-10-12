<?php

namespace App\Traits;

use App\Models\Period;
use App\Models\SchoolYear;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\Auth;

trait RecordSignature
{
    protected static function boot()
    {
        parent::boot();

        if (!session()->has('year')) {
            session(['year' => SchoolYear::where('year', Setting::get('year_selected'))->first() ]);
        }

        if (!session()->has('period')) {
            session(['period' =>
                Period::where('start_date', '<=', date('Y-m-d'))
                ->where('end_date', '>=', date('Y-m-d'))
                ->first()]);
        }

        if (backpack_user()) {
            static::creating(function ($model) {
                try {
                    $model->created_by = backpack_user()?->id;
                } catch (\Exception $th) {
                    $model->created_by == null;
                }
            });

            static::updating(function ($model) {
                try {
                    $model->updated_by = backpack_user()?->id;
                } catch (\Exception $th) {
                    $model->updated_by == null;
                }
            });

            static::deleting(function ($model) {
                try {
                    $model->deleted_by = backpack_user()?->id;
                    $model->saveQuietly();
                } catch (\Exception $th) {
                    $model->deleted_by == null;
                }
            });
        }
    }
}
