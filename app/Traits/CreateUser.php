<?php

namespace  App\Traits;

use App\Mail\NewAccountMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

trait CreateUser
{
    protected static function boot(){
        parent::boot();

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

                    $usuario = User::where('email', $model->email)->first();

                        if ($usuario == null) {
                            $usuario = new User;
                            $usuario->password = bcrypt(str_replace('-', '', $model->dui));
                        }

                        $usuario->name = $model->name." ".$model->last_name;
                        $usuario->email = $model->email;
                        $usuario->save();

                        $model->user_id = $usuario->id;

                        if ($table == 'teachers') {
                            $usuario->assignRole(User::ROLE_DOCENTE);
                        }

                        $emailData = [
                            'name' => $usuario->name,
                            'email' => $usuario->email,
                            'password' => bcrypt(str_replace('-', '', $model->dui))
                        ];

                        // Mail::to($usuario->email)->cc([Setting::get('copy_email')])->send(new NewAccountMail($emailData));
                    try {
                    } catch (\Throwable $th) {
                        Log::error($th);
                    }
                } catch (\Exception $th) {
                    $model->created_by == null;
                }
            });

            static::updating(function ($model) {
                try {
                    $table = $model->getTable();

                    $model->updated_by = backpack_user()?->id;

                    try {
                        $usuario = User::where('id', $model->user_id)->first();

                        if ($usuario == null) {
                            $usuario = new User;
                            $usuario->password = bcrypt(str_replace('-', '', $model->dui));
                        }

                        $usuario->update([
                            'name' => $model->name." ".$model->last_name,
                            'email' => $model->email,
                            // 'password' => bcrypt(substr(str_replace('-', '', str_replace('.', '', $model->rut)),0,4))
                        ]);

                        if ($table == 'teachers') {
                            $usuario->assignRole(User::ROLE_DOCENTE);
                        }
                    } catch (\Throwable $th) {
                        Log::error($th);
                    }
                    if ($table == 'teachers') {
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
