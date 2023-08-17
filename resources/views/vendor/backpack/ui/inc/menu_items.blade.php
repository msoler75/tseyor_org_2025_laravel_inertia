{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<!-- <x-backpack::menu-item title="Users" icon="la la-user" :link="backpack_url('user')" /> -->
<x-backpack::menu-item title="Comunicados" icon="la la-file" :link="backpack_url('comunicado')" />

<x-backpack::menu-item title="Guias" icon="la la-star" :link="backpack_url('guia')" />


<x-backpack::menu-dropdown title="Usuarios y permisos" icon="la la-puzzle-piece">
    <!-- <x-backpack::menu-dropdown-header title="Permisos" /> -->
    <x-backpack::menu-dropdown-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permisos" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>
