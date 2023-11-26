{{-- This file is used for menu items by any Backpack v6 theme --}}
@php
    if (!session()->has('year')) {
        session(['year' => \App\Models\SchoolYear::where('year', \Backpack\Settings\app\Models\Setting::get('year_selected'))->first() ]);
    }

    $year = session('year');
@endphp

<x-backpack::menu-dropdown :title="($year) ? $year->year : 'Ningún año seleccionado.'" icon="">
    @if ($year)
        @foreach (\App\Models\SchoolYear::all() as $item)
            @php
                $url = route('update.year.period.session', [$item?->id]);

                echo "
                    <a class='dropdown-item' href='{$url}'>
                        <span>{$item?->year}</span>
                    </a>
                ";
            @endphp
        @endforeach
    @endif
</x-backpack::menu-dropdown>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<x-backpack::menu-dropdown title="Autenticación" icon="la la-puzzle-piece">
    <x-backpack::menu-dropdown-item title="Users" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Configuración" icon="la la-cog">
    <x-backpack::menu-dropdown-item title='Configuraciones' icon='la la-cog' :link="backpack_url('setting')" />
    <x-backpack::menu-dropdown-item title='Logs' icon='la la-terminal' :link="backpack_url('log')" />
    <x-backpack::menu-dropdown-item title="Periods" icon="la la-question" :link="backpack_url('period')" />
    <x-backpack::menu-dropdown-item title="School years" icon="la la-question" :link="backpack_url('school-year')" />
    <x-backpack::menu-dropdown-item title="Calendars" icon="la la-question" :link="backpack_url('calendar')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Evaluaciones" icon="la la-chalkboard-teacher">
    <x-backpack::menu-dropdown-item title="Evaluaciones" icon="la la-chalkboard-teacher" :link="backpack_url('evaluation')" />
    <x-backpack::menu-dropdown-item title="Notas" icon="la la-terminal" :link="backpack_url('notas-alumno')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Reportes" icon="la la-user-graduate">
    <x-backpack::menu-dropdown-item title="Reporte de notas" icon="la la-user-graduate" :link="backpack_url('reporte/notas')" />
    <x-backpack::menu-dropdown-item title="Reporte de asistencia" icon="la la-user-graduate" :link="backpack_url('reporte/asistencia')" />
</x-backpack::menu-dropdown>


<x-backpack::menu-item title="Cursos" icon="la la-graduation-cap" :link="backpack_url('course')" />
<x-backpack::menu-item title="Estudiantes" icon="la la-user-graduate" :link="backpack_url('student')" />
<x-backpack::menu-item title="Materias" icon="la la-book-reader" :link="backpack_url('subject')" />
<x-backpack::menu-item title="Profesores" icon="la la-user-tie" :link="backpack_url('teacher')" />

<x-backpack::menu-item title="Asistencias" icon="la la-user-check" :link="backpack_url('assistance')" />



<x-backpack::menu-item title="Inscripciones" icon="la la-question" :link="backpack_url('inscription')" />
