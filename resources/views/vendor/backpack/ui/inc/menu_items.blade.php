{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}</a></li>


<x-backpack::menu-dropdown title="Contenidos" icon="la la-tree">
    <x-backpack::menu-dropdown-item title="Audios" icon="la la-music" :link="backpack_url('audio')" />
    <x-backpack::menu-dropdown-item title="Comunicados" icon="la la-file" :link="backpack_url('comunicado')" />
    <x-backpack::menu-dropdown-item title="Entradas de Blog" icon="la la-pencil-alt" :link="backpack_url('entrada')" />
    <x-backpack::menu-dropdown-item title="Libros" icon="la la-book" :link="backpack_url('libro')" />
    <x-backpack::menu-dropdown-item title="Noticias" icon="la la-volume-up" :link="backpack_url('noticia')" />
    <x-backpack::menu-dropdown-header title="Glosario" />
    <x-backpack::menu-dropdown-item title="Guias Estelares" icon="la la-star" :link="backpack_url('guia')" />
    <x-backpack::menu-dropdown-item title="Lugares de la Galaxia" icon="la la-map-marker" :link="backpack_url('lugar')" />
</x-backpack::menu-dropdown>


<x-backpack::menu-dropdown title="Eventos y Social" icon="la la-facebook">
    <x-backpack::menu-dropdown-item title="Eventos" icon="la la-calendar-check" :link="backpack_url('evento')" />
    <x-backpack::menu-dropdown-item title="Inscripciones" icon="la la-edit" :link="backpack_url('inscripcion')" />
    <x-backpack::menu-dropdown-item title="Comentarios" icon="la la-comments" :link="backpack_url('comentario')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Directorio" icon="la la-map-marked-alt">
    <x-backpack::menu-dropdownitem title="Centros" icon="la la-map-marker" :link="backpack_url('centro')" />
    <x-backpack::menu-dropdown-item title="Contactos" icon="la la-address-book" :link="backpack_url('contacto')" />
    <x-backpack::menu-dropdown-item title="Salas virtuales" icon="la la-hospital-alt" :link="backpack_url('sala')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Gestión de Equipos" icon="la la-users">
    <x-backpack::menu-dropdown-item title="Equipos" icon="la la-users" :link="backpack_url('equipo')" />
    <x-backpack::menu-dropdown-item title="Solicitudes" icon="la la-hand-paper" :link="backpack_url('solicitud')" />
    <x-backpack::menu-dropdown-item title="Publicaciones" icon="la la-pencil-alt" :link="backpack_url('publicacion')" />
    <x-backpack::menu-dropdown-item title="Normativas" icon="la la-balance-scale" :link="backpack_url('normativa')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Archivos" icon="la la-folder">
    <x-backpack::menu-dropdown-item title="Nodos" icon="la la-cube" :link="backpack_url('nodo')" />
    <x-backpack::menu-dropdown-item title="Lista de acceso" icon="la la-list" :link="backpack_url('acl')" />
    <x-backpack::menu-dropdown-item title="Grupos" icon="la la-user-friends" :link="backpack_url('grupo')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Usuarios y permisos" icon="la la-user-lock">
    <x-backpack::menu-dropdown-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permisos" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>

