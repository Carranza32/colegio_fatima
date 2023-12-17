<?php

namespace App\Models;

use App\Traits\CurrentYear;
use App\Traits\StatusDescription;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use CrudTrait, StatusDescription, SoftDeletes, CurrentYear;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'students';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $appends = ['full_name'];

    protected $casts = [
        'documentos' => 'array',
        'parent_data' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function setDocumentosAttribute($value)
    {
        $this->attributes['documentos'] = is_array($value) ? implode(',',$value) : '' ;
    }
    public function setCondicionDeDiscapacidadAttribute($value)
    {
        $this->attributes['condicion_de_discapacidad'] = is_array($value) ? implode(',',$value) : '' ;
    }
    public function setEstudianteReferidoAAttribute($value)
    {
        $this->attributes['estudiante_referido_a'] = is_array($value) ? implode(',',$value) : '' ;
    }
    public function setEstudianteRecibeAttribute($value)
    {
        $this->attributes['estudiante_recibe'] = is_array($value) ? implode(',',$value) : '' ;
    }

    public function getAttendances($date, $subject_id){
        $attendances = Assistance::where('course_id', $this->course_id)
            ->where('subject_id', $subject_id)
            ->where('date', $date)
            ->get()
            ->pluck('id')
            ->toArray();

        $attendanceDetails = AssistanceDetail::whereIn('assistance_id', $attendances)
            ->where('student_id', $this->id)
            ->first();

        return $attendanceDetails;
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    function user() {
        return $this->belongsTo(User::class);
    }

    function course() {
        return $this->belongsTo(Course::class);
    }
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
    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->last_name;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
