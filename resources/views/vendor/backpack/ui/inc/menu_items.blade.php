{{-- This file is used for menu items by any Backpack v6 theme --}}
@php
    if (!session()->has('year')) {
        session(['year' => \App\Models\SchoolYear::where('year', \Backpack\Settings\app\Models\Setting::get('year_selected'))->first() ]);
    }

    if (!session()->has('period')) {
        session(['period' =>
            \App\Models\Period::where('start_date', '<=', date(session('year')?->year.'-m-d'))
            ->where('end_date', '>=', date(session('year')?->year.'-m-d'))
            ->first()]);
    }

    $year = session('year');
    $period = session('period');
@endphp

<x-backpack::menu-dropdown :title="($year) ? $year->year : 'Ningún año seleccionado.'" icon="">
    @if ($year)
        @foreach (\App\Models\SchoolYear::all() as $item)
            @php
                $url = route('update.year.session', [$item?->id]);

                echo "
                    <a class='dropdown-item' href='{$url}'>
                        <span>{$item?->year}</span>
                    </a>
                ";
            @endphp
        @endforeach
    @endif
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown :title="($period) ? $period->name : 'Ningún periodo seleccionado.'" icon="">
    @if ($period)
        @foreach (\App\Models\Period::all() as $item)
            @php
                $url = route('update.period.session', [$item?->id]);

                echo "
                    <a class='dropdown-item' href='{$url}'>
                        <span>{$item?->name}</span>
                    </a>
                ";
            @endphp
        @endforeach
    @endif
</x-backpack::menu-dropdown>

@if ( backpack_user()->hasRole(\App\Models\User::SUPERGOD_ROLE) || backpack_user()->hasRole(\App\Models\User::ROLE_ADMIN) || backpack_user()->hasRole(\App\Models\User::ROLE_DIRECTOR) )
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
    <x-backpack::menu-dropdown title="Autenticación" icon="la la-puzzle-piece">
        <x-backpack::menu-dropdown-item title="Users" icon="la la-user" :link="backpack_url('user')" />
        <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
        <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
    </x-backpack::menu-dropdown>
@endif

@if(backpack_user()->hasAnyPermission(['setting.read', 'log.read', 'period.read', 'school-year.read', 'calendar.read']))
    <x-backpack::menu-dropdown title="Configuración" icon="la la-cog">
        @if(backpack_user()->can('setting.read'))
            <x-backpack::menu-dropdown-item title='Configuraciones' icon='la la-cog' :link="backpack_url('setting')" />
        @endif

        @if(backpack_user()->can('log.read'))
            <x-backpack::menu-dropdown-item title='Logs' icon='la la-terminal' :link="backpack_url('log')" />
        @endif

        @if(backpack_user()->can('period.read'))
            <x-backpack::menu-dropdown-item title="Periodos" icon="la la-calendar-week" :link="backpack_url('period')" />
        @endif

        @if(backpack_user()->can('school-year.read'))
            <x-backpack::menu-dropdown-item title="Año escolar" icon="la la-calendar" :link="backpack_url('school-year')" />
        @endif

        @if(backpack_user()->can('calendar.read'))
            <x-backpack::menu-dropdown-item title="Calendario" icon="la la-calendar-plus" :link="backpack_url('calendar')" />
        @endif
    </x-backpack::menu-dropdown>
@endif

@if(backpack_user()->can('notas-alumno.read'))
    <x-backpack::menu-dropdown title="Evaluaciones" icon="la la-chalkboard-teacher">
        <x-backpack::menu-dropdown-item title="Notas" icon="la la-terminal" :link="backpack_url('notas-alumno')" />
    </x-backpack::menu-dropdown>
@endif

@if(backpack_user()->hasAnyPermission(['reports', 'reporte-notas.read', 'reporte-asistencia.read']))
    <x-backpack::menu-dropdown title="Reportes" icon="la la-user-graduate">
        @if (backpack_user()->can('reporte-notas.read'))
            <x-backpack::menu-dropdown-item title="Reporte de notas" icon="la la-user-graduate" :link="backpack_url('reporte/notas')" />
        @endif

        @if (backpack_user()->can('reporte-asistencia.read'))
            <x-backpack::menu-dropdown-item title="Reporte de asistencia" icon="la la-user-graduate" :link="backpack_url('reporte/asistencia')" />
        @endif

        {{-- <x-backpack::menu-dropdown-item title="Exportar asistencia SIGIES" icon="la la-user-graduate" :link="route('assistance.import.index')" /> --}}
    </x-backpack::menu-dropdown>
@endif

@if (backpack_user()->can('course.read'))
    <x-backpack::menu-item title="Cursos" icon="la la-graduation-cap" :link="backpack_url('course')" />
@endif

@if (backpack_user()->can('student.read'))
    <x-backpack::menu-item title="Estudiantes" icon="la la-user-graduate" :link="backpack_url('student')" />
@endif

@if (backpack_user()->can('subject.read'))
    <x-backpack::menu-item title="Materias" icon="la la-book-reader" :link="backpack_url('subject')" />
@endif

@if (backpack_user()->can('teacher.read'))
    <x-backpack::menu-item title="Profesores" icon="la la-user-tie" :link="backpack_url('teacher')" />
@endif

@if (backpack_user()->can('assistance.read'))
    <x-backpack::menu-item title="Asistencias" icon="la la-user-check" :link="backpack_url('assistance')" />
@endif
