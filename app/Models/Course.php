<?php

namespace App\Models;

use App\Traits\CurrentYear;
use App\Traits\RecordSignature;
use App\Traits\StatusDescription;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use CrudTrait, StatusDescription, SoftDeletes, HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'courses';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $appends = ['name_letter'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getNameLetterAttribute()
    {
        return $this->attributes['name'] . ' ' . $this->letter;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function setScheduleFileAttribute($value)
    {
        if ($value != null) {
            $attribute_name = "schedule_file";
            $disk = "public";
            $destination_path = "horario";

            $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
        }
    }
}
