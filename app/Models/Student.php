<?php

namespace App\Models;

use App\Traits\CurrentYear;
use App\Traits\StatusDescription;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class Student extends Model
{
    use CrudTrait, StatusDescription, SoftDeletes;

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
        'condicion_de_discapacidad' => 'array',
        'estudiante_referido_a' => 'array',
        'estudiante_recibe' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
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

    function matricula_button() {
        return '<a href="'.route('reporte.ficha_matricula', [$this->id]).'" class="btn btn-sm btn-link"><span><i class="la la-file-download"></i> Matrícula</span></a>';
    }

    function getParentData($parentType) {
        $parent = $this->parent_data->where('family_type', $parentType)->first();

        return $parent;
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

    function conduct_records() {
        return $this->hasMany(ConductRecord::class);
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

    // Accesor para obtener el array desde la base de datos
    public function getDocumentosAttribute($value)
    {
        return json_decode($value, true);
    }

    // Mutador para guardar el array en la base de datos
    public function setDocumentosAttribute($value)
    {
        $this->attributes['documentos'] = json_encode($value);
    }

    public function setCondicionDeDiscapacidadAttribute($value)
    {
        $this->attributes['condicion_de_discapacidad'] = json_encode($value);
    }
    public function setEstudianteReferidoAAttribute($value)
    {
        $this->attributes['estudiante_referido_a'] = json_encode($value);
    }
    public function setEstudianteRecibeAttribute($value)
    {
        $this->attributes['estudiante_recibe'] = json_encode($value);
    }

}
