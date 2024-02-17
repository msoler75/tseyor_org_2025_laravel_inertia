{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }} de administración</a></li>

@canany(['administrar contenidos', 'administrar social', 'administrar directorio', 'administrar equipos', 'administrar archivos', 'administrar usuarios'])


@can('administrar contenidos')
<x-backpack::menu-dropdown title="Contenidos" icon="la la-tree">
    <x-backpack::menu-dropdown-item title="Audios" icon="la la-music" :link="backpack_url('audio')" />
    <x-backpack::menu-dropdown-item title="Comunicados" icon="la la-file" :link="backpack_url('comunicado')" />
    <x-backpack::menu-dropdown-item title="Entradas de Blog" icon="la la-pencil-alt" :link="backpack_url('entrada')" />
    <x-backpack::menu-dropdown-item title="Libros" icon="la la-book" :link="backpack_url('libro')" />
    <x-backpack::menu-dropdown-item title="Meditaciones" icon="la la-smile-beam" :link="backpack_url('meditacion')" />
    <x-backpack::menu-dropdown-item title="Noticias" icon="la la-volume-up" :link="backpack_url('noticia')" />
    <x-backpack::menu-dropdown-item title="Páginas/SEO" icon="la la-pager" :link="backpack_url('pagina')" />
    <x-backpack::menu-dropdown-item title="Radio Tseyor" icon="la la-broadcast-tower" :link="backpack_url('radio-item')" />
    <x-backpack::menu-dropdown-item title="Videos" icon="la la-youtube" :link="backpack_url('video')" />
</x-backpack::menu-dropdown>
@endcan

@can('administrar contenidos')
<x-backpack::menu-dropdown title="Glosario" icon="la la-list-ul">
    <x-backpack::menu-dropdown-item title="Términos" icon="la la-edit" :link="backpack_url('termino')" />
    <x-backpack::menu-dropdown-item title="Guias Estelares" icon="la la-star" :link="backpack_url('guia')" />
    <x-backpack::menu-dropdown-item title="Lugares de la Galaxia" icon="la la-map-marker" :link="backpack_url('lugar')" />
</x-backpack::menu-dropdown>
@endcan

@can('administrar social')
<x-backpack::menu-dropdown title="Eventos y Social" icon="la la-facebook">
    <x-backpack::menu-dropdown-item title="Eventos" icon="la la-calendar-check" :link="backpack_url('evento')" />
    <x-backpack::menu-dropdown-item title="Inscripciones" icon="la la-edit" :link="backpack_url('inscripcion')" />
    <x-backpack::menu-dropdown-item title="Comentarios" icon="la la-comments" :link="backpack_url('comentario')" />
    <x-backpack::menu-dropdown-item title="Correos" icon="la la-envelope" :link="backpack_url('email')" />
</x-backpack::menu-dropdown>
@endcan

@can('administrar directorio')
<x-backpack::menu-dropdown title="Directorio" icon="la la-map-marked-alt">
    <x-backpack::menu-dropdownitem title="Centros" icon="la la-map-marker" :link="backpack_url('centro')" />
    <x-backpack::menu-dropdown-item title="Contactos" icon="la la-address-book" :link="backpack_url('contacto')" />
    <x-backpack::menu-dropdown-item title="Salas virtuales" icon="la la-hospital-alt" :link="backpack_url('sala')" />
</x-backpack::menu-dropdown>
@endcan


@canany(['administrar contenidos', 'administrar experiencias'])
<x-backpack::menu-dropdown title="Comunidad" icon="la la-globe">
    @canany(['administrar contenidos', 'administrar experiencias'])
    <x-backpack::menu-dropdown-item title="Meditaciones" icon="la la-smile-beam" :link="backpack_url('meditacion')" />
    @endcanany
    @can('administrar contenidos')
    <x-backpack::menu-dropdown-item title="Normativas" icon="la la-balance-scale" :link="backpack_url('normativa')" />
    <x-backpack::menu-dropdown-item title="Publicaciones" icon="la la-pencil-alt" :link="backpack_url('publicacion')" />
    @endcan
    @can('administrar experiencias')
    <x-backpack::menu-dropdown-item title="Experiencias" icon="lab la-fly" :link="backpack_url('experiencia')" />
    @endcan
</x-backpack::menu-dropdown>
@endcanany

@can('administrar equipos')
<x-backpack::menu-dropdown title="Gestión de Equipos" icon="la la-users">
    <x-backpack::menu-dropdown-item title="Equipos" icon="la la-users" :link="backpack_url('equipo')" />
    <x-backpack::menu-dropdown-item title="Solicitudes" icon="la la-hand-paper" :link="backpack_url('solicitud')" />
    <x-backpack::menu-dropdown-item title="Informes" icon="la la-file-invoice" :link="backpack_url('informe')" />
</x-backpack::menu-dropdown>
@else
    @php
        $user = Auth::user();
    @endphp
    @if ($user && $user->equiposQueCoordina->count() > 0)
        <x-backpack::menu-item title="Informes de equipo" icon="la la-file-invoice" :link="backpack_url('informe')" />
    @endif
@endcan

@can('administrar archivos')
<x-backpack::menu-dropdown title="Archivos" icon="la la-folder">
    <x-backpack::menu-dropdown-item title="Ver/Subir archivos" icon="la la-cloud-upload" link="/admin/archivos" />
    <x-backpack::menu-dropdown-item title="Nodos" icon="la la-cube" :link="backpack_url('nodo')" />
    <x-backpack::menu-dropdown-item title="Lista de acceso" icon="la la-list" :link="backpack_url('acl')" />
</x-backpack::menu-dropdown>
@endcan

@can('administrar usuarios')
<x-backpack::menu-dropdown title="Usuarios y permisos" icon="la la-user-lock">
    <x-backpack::menu-dropdown-item title="Usuarios" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Grupos" icon="la la-user-friends" :link="backpack_url('grupo')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permisos" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>
@endcan

@can('avanzado')
<x-backpack::menu-dropdown title="Avanzado" icon="la la-cog">
    <x-backpack::menu-dropdown-item title="Ajustes" icon="la la-sliders-h" :link="backpack_url('setting')" />
    <x-backpack::menu-dropdown-item title="Revisiones" icon="la la-eye" :link="backpack_url('revision')" />
</x-backpack::menu-dropdown>
@endcan


@else

@php

header('Location: /');
exit;

@endphp


@endcanany

