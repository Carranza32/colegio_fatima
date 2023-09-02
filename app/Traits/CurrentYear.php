<?php

namespace App\Traits;

use App\Mail\NewAccountMail;
use App\Models\Period;
use App\Models\SchoolYear;
use App\Models\User;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

trait CurrentYear{
    protected static function boot(){
        parent::boot();

        $model = (new self());

        if (!session()->has('year')) {
            session(['year' => SchoolYear::where('year', Setting::get('year_selected'))->first() ]);
        }

        if (!session()->has('period')) {
            session(['period' =>
                Period::where('fecha_inicio', '<=', date('Y-m-d'))
                ->where('fecha_fin', '>=', date('Y-m-d'))
                ->first()]);
        }

        //dd(date(session('year')?->year.'-m-d'));

        static::addGlobalScope('current_year', function ($query) use ($model) {
            $table = $model->getTable();

            $has_column = Schema::hasColumn( $table , 'date_scope');

            if ($has_column) {
                return $query->whereYear("{$table}.date_scope", session('year')?->year);
            }
        });

        if (backpack_user()) {
            static::creating(function ($model) {
                $table = $model->getTable();

                $has_column = Schema::hasColumn( $table , 'date_scope');

                try {
                    if ($has_column) {
                        if ($model?->date_scope == null) {
                            $model->date_scope = date(session('year')?->year.'-m-d');
                        }
                    }

                    $model->created_by = backpack_user()?->id;

                    if ($table == 'alumnos' || $table == 'docentes') {
                            $usuario = User::where('email', $model->email)->first();

                            if ($usuario == null) {
                                $usuario = new User;
                                $usuario->password = bcrypt(substr(str_replace('-', '', str_replace('.', '', $model->rut)),0,4));
                            }

                            $usuario->name = $model->nombres." ".$model->apellidos;
                            $usuario->email = $model->email;
                            $usuario->save();

                            $model->user_id = $usuario->id;

                            if ($table == 'alumnos') {
                                $usuario->assignRole(User::ROLE_ALUMNO);
                            }

                            if ($table == 'docentes') {
                                $usuario->assignRole(User::ROLE_DOCENTE);
                            }

                            $emailData = [
                                'name' => $usuario->name,
                                'email' => $usuario->email,
                                'password' => substr(str_replace('-', '', str_replace('.', '', $model->rut)),0,4)
                            ];

                            Mail::to($usuario->email)->cc([Setting::get('copy_email')])->send(new NewAccountMail($emailData));
                        try {
                        } catch (\Throwable $th) {
                            Log::error($th);
                        }
                    }
                } catch (\Exception $th) {
                    $model->created_by == null;
                }
            });

            static::updating(function ($model) {
                try {
                    $table = $model->getTable();

                    $model->updated_by = backpack_user()?->id;

                    if ($table == 'alumnos' || $table == 'docentes') {
                        try {
                            $usuario = User::where('id', $model->user_id)->first();

                            $usuario->update([
                                'name' => $model->nombres." ".$model->apellidos,
                                'email' => $model->email,
                                // 'password' => bcrypt(substr(str_replace('-', '', str_replace('.', '', $model->rut)),0,4))
                            ]);

                            if ($table == 'alumnos') {
                                $usuario->assignRole(User::ROLE_ALUMNO);
                            }

                            if ($table == 'docentes') {
                                $usuario->assignRole(User::ROLE_DOCENTE);
                            }
                        } catch (\Throwable $th) {
                            Log::error($th);
                        }
                    }
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
