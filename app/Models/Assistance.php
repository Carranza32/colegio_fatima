<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assistance extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'assistances';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $appends = ['assistances', 'absences'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getAssistancesAttribute()
    {
        return AssistanceDetail::where('assistance_id', $this->id)->where('has_assistance', 1)->count();
    }

    public function getAbsencesAttribute()
    {
        return AssistanceDetail::where('assistance_id', $this->id)->where('has_assistance', 0)->count();
    }

    public function getAlumnAssistance($alumno_id) : bool
    {
        return AssistanceDetail::where('assistance_id', $this->id)->where('alumno_id', $alumno_id)->first()?->has_assistance;
    }

    public function getAsignaturaAssistance()
    {
        return Assistance::where('course_id', $this->course_id)->where('subject_id', $this->subject_id)->where('date', $this->date)->get();
    }

    public function getAsignaturaAssistanceCount(int $subject_id)
    {
        return Assistance::where('course_id', $this->course_id)->whereDate('date', $this->date)->where('subject_id', $subject_id)->get()->count();
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    function subject() {
        return $this->belongsTo(Subject::class);
    }

    function course() {
        return $this->belongsTo(Course::class);
    }

    public function assistance_detail()
    {
        return $this->hasMany(AssistanceDetail::class, 'assistance_id');
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
