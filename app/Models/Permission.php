<?php

namespace App\Models;

use App\Traits\RecordSignature;
use App\Traits\RecordSignatureRelations;
use App\Traits\StatusDescription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory, RecordSignature, RecordSignatureRelations, StatusDescription, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
    ];

    protected $casts = [
        'roles' => 'array'
    ];
}
