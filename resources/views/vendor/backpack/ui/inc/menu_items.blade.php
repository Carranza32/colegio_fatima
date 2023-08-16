{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<x-backpack::menu-dropdown title="Add-ons" icon="la la-puzzle-piece">
    <x-backpack::menu-dropdown-header title="Authentication" />
    <x-backpack::menu-dropdown-item title="Users" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-item title='Settings' icon='la la-cog' :link="backpack_url('setting')" />
<x-backpack::menu-item title='Logs' icon='la la-terminal' :link="backpack_url('log')" />
<x-backpack::menu-item title="Courses" icon="la la-question" :link="backpack_url('course')" />
<x-backpack::menu-item title="Students" icon="la la-question" :link="backpack_url('student')" />
<x-backpack::menu-item title="Subjects" icon="la la-question" :link="backpack_url('subject')" />
<x-backpack::menu-item title="Teachers" icon="la la-question" :link="backpack_url('teacher')" />
<x-backpack::menu-item title="Evaluations" icon="la la-question" :link="backpack_url('evaluation')" />
<x-backpack::menu-item title="Assistances" icon="la la-question" :link="backpack_url('assistance')" />