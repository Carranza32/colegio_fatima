<?php

namespace App\Traits;

use App\Models\Period;
use App\Models\SchoolYear;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\Schema;

trait CurrentPeriod{
    protected static function boot(){
        parent::boot();

        $model = (new self());

        if (!session()->has('year')) {
            session(['year' => SchoolYear::where('year', Setting::get('year_selected'))->first() ]);
        }

        if (!session()->has('period')) {
            session(['period' =>
                Period::where('fecha_inicio', '<=', date(session('year')?->year.'-m-d'))
                ->where('fecha_fin', '>=', date(session('year')?->year.'-m-d'))
                ->first()]);
        }

        static::addGlobalScope('current_period', function ($query) use ($model) {
            $table = $model->getTable();

            $has_column = Schema::hasColumn( $table , 'date_scope');

            if ($has_column) {
                return $query->whereBetween("{$table}.date_scope", [session('period')?->fecha_inicio, session('period')?->fecha_fin.' 23:59:59']);
            }
        });

        if (backpack_user()) {
            static::creating(function ($model) {
                try {
                    if ($model?->date_scope == null) {
                        $model->date_scope = date(session('year')?->year.'-m-d');
                    }

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
