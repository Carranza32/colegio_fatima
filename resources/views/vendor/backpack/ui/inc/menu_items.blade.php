{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<x-backpack::menu-dropdown title="Add-ons" icon="la la-puzzle-piece">
    <x-backpack::menu-dropdown-header title="Authentication" />
    <x-backpack::menu-dropdown-item title="Users" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-item title='Configuraciones' icon='la la-cog' :link="backpack_url('setting')" />
<x-backpack::menu-item title='Logs' icon='la la-terminal' :link="backpack_url('log')" />
<x-backpack::menu-item title="Cursos" icon="la la-graduation-cap" :link="backpack_url('course')" />
<x-backpack::menu-item title="Estudiantes" icon="la la-user-graduate" :link="backpack_url('student')" />
<x-backpack::menu-item title="Materias" icon="la la-book-reader" :link="backpack_url('subject')" />
<x-backpack::menu-item title="Profesores" icon="la la-user-tie" :link="backpack_url('teacher')" />
<x-backpack::menu-item title="Evaluaciones" icon="la la-chalkboard-teacher" :link="backpack_url('evaluation')" />
<x-backpack::menu-item title="Asistencias" icon="la la-user-check" :link="backpack_url('assistance')" />

<x-backpack::menu-item title="Periods" icon="la la-question" :link="backpack_url('period')" />
<x-backpack::menu-item title="School years" icon="la la-question" :link="backpack_url('school-year')" />
<x-backpack::menu-item title="Calendars" icon="la la-question" :link="backpack_url('calendar')" />