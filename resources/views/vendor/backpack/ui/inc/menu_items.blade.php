{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}</a></li>

<!-- <x-backpack::menu-item title="Users" icon="la la-user" :link="backpack_url('user')" /> -->
<x-backpack::menu-item title="Comunicados" icon="la la-file" :link="backpack_url('comunicado')" />

<x-backpack::menu-item title="Guias" icon="la la-star" :link="backpack_url('guia')" />

<x-backpack::menu-item title="Equipos" icon="la la-users" :link="backpack_url('equipo')" />

<x-backpack::menu-dropdown title="Usuarios y permisos" icon="la la-puzzle-piece">
    <!-- <x-backpack::menu-dropdown-header title="Permisos" /> -->
    <x-backpack::menu-dropdown-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permisos" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Archivos" icon="la la-folder">
    <x-backpack::menu-dropdown-item title="Nodos" icon="la la-cube" :link="backpack_url('nodo')" />
    <x-backpack::menu-dropdown-item title="Lista de acceso" icon="la la-list" :link="backpack_url('acl')" />
    <x-backpack::menu-dropdown-item title="Grupos" icon="la la-user-friends" :link="backpack_url('grupo')" />
    <x-backpack::menu-dropdown-item title="Usuarios y grupos" icon="la la-object-group" :link="backpack_url('grupo-user')" />
</x-backpack::menu-dropdown>

