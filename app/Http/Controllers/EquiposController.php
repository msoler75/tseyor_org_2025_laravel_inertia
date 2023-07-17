<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Equipo;
use App\Models\Carpeta;
use App\Models\User;
use App\Pigmalion\SEO;
use App\Policies\LinuxPolicy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EquiposController extends Controller
{
    /**
     * Lista de equipos
     */
    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Equipo::withCount('usuarios')
            ->where('categoria', '=', $categoria)
            ->paginate(10)->appends(['categoria' => $categoria])
            : ($filtro ? Equipo::withCount('usuarios')
                ->where('nombre', 'like', '%' . $filtro . '%')
                ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                ->paginate(10)->appends(['buscar' => $filtro])
                :
                Equipo::withCount('usuarios')->latest()->paginate(10)
            );

        $categorias = Equipo::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        return Inertia::render('Equipos/Index', [
            'filtrado' => $filtro,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('equipos'));
    }

    /**
     * Muestra un equipo o departamento
     */
    public function show($id)
    {
        $equipo = Equipo::with(['usuarios' => function ($query) {
            $query->select('users.id', 'users.name as nombre', 'users.slug', 'profile_photo_path as avatar')
                ->take(30);
        }])
            ->where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        $carpetas = Carpeta::where('group_id', '=', $equipo->id)->take(3)->get();

        $ultimosArchivos = [];

        $user = auth()->user();
        $acl = LinuxPolicy::acl($user, ['leer', 'ejecutar']);
        foreach ($carpetas as $carpeta) {
            $nodo = LinuxPolicy::nodoHeredado($carpeta->ruta);
            if ($nodo && LinuxPolicy::ejecutar($nodo, $user, $acl)) {
                $archivos = $carpeta->ultimosArchivos();
                $ultimosArchivos = array_merge($ultimosArchivos, $archivos);
            }
        }

        // ordenamos con recientes primero
        usort($ultimosArchivos, function ($a, $b) {
            return $b['fecha_modificacion'] - $a['fecha_modificacion'];
        });

        $totalMiembros = $equipo->usuarios()->count();

        return Inertia::render('Equipos/Equipo', [
            'equipo' => $equipo,
            'carpetas' => $carpetas,
            'ultimosArchivos' =>  array_slice($ultimosArchivos, 0, 3),
            'totalMiembros' => $totalMiembros,
            'usuarios' => Inertia::lazy(function () use ($id) {
                return Equipo::where('slug', $id)
                    ->orWhere('id', $id)
                    ->firstOrFail()->usuarios()->get();
                //return Equipo::findOrFail($id)->usuarios()->get();
            })
        ])
            ->withViewData(SEO::from($equipo));
    }


    /**
     * Página de la UTG
     */
    public function index_utg()
    {
        $categoria = 'utg';
        $departamentos = Equipo::where('categoria', '=', $categoria)
            ->take(6)->get();

        return Inertia::render(
            'Utg/Index',
            [
                'departamentos' => $departamentos
            ]
        )
            ->withViewData(SEO::get('utg'));
    }



    /**
     ** Crea un nuevo equipo
     */
    public function store(Request $request)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'nombre' => 'required|max:32',
            'descripcion' => 'max:400',
            'imagen' => 'max:255',
        ]);

        $user = auth()->user();

        // Crear una nueva instancia de Inscripcion y guardarla en la base de datos
        $equipo = Equipo::create([
            'nombre' => $validatedData['nombre'],
            'slug' => Str::slug($validatedData['nombre']),
            'imagen' => $validatedData['imagen'],
            'descripcion' => $validatedData['descripcion'],
            'user_id' => $user->id ?? 1
        ]);

        if ($equipo) {
            return to_route('equipo', $equipo->slug);
        } else {
            // Devolver un objeto JSON con los errores de validación
            return redirect()->back()->withErrors(['msg', 'No se pudo crear el equipo, inténtalo de nuevo']);
        }
    }


    ////////////////////////////////////////////////////////////////////
    ///// API
    ////////////////////////////////////////////////////////////////////


 /**
     ** Crea un nuevo equipo
     */
    public function update(Request $request, $idEquipo)
    {
        $equipo = Equipo::findOrFail($idEquipo);

        // Validar los datos
        $validatedData = $request->validate([
            'nombre' => 'required|max:32',
            'descripcion' => 'required|max:400',
            'anuncio' => 'max:400',
            'reuniones' => 'max:400',
            'imagen' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Ajustar las reglas de validación según tus necesidades
        ]);

        $user = auth()->user();

        // Verificar si el usuario es un coordinador del equipo
        /*if (!$equipo->coordinadores->contains($user)) {
            return response()->json(['error' => 'No tienes permisos para editar este equipo.'], 403);
        }*/

        // Actualizar los datos del equipo
        $equipo->nombre = $validatedData['nombre'];
        $equipo->descripcion = $validatedData['descripcion'];
        $equipo->anuncio = $validatedData['anuncio'];
        $equipo->reuniones = $validatedData['reuniones'];
        // $equipo->imagen = $validatedData['imagen'];

        // Subir la nueva imagen (si se proporciona)
        $newImage = $request->file('imagen');
        if ($newImage) {
            $path = $newImage->store('public/imagenes/equipos');
            $equipo->imagen = Storage::url($path);
        }

        $equipo->save();

        return response()->json($equipo->toArray(), 200);
    }


    /**
     * Agrega un usuario a un equipo
     */
    public function addMember($idEquipo, $idUsuario)
    {
        // Obtenemos el usuario y el equipo
        $equipo = Equipo::findOrFail($idEquipo);
        $usuario = User::findOrFail($idUsuario);

        // agregamos el usuario al equipo
        $equipo->usuarios()->syncWithoutDetaching([$idUsuario]);

        return response()->json(['mensaje' => 'El usuario fue añadido al equipo'], 200);
    }


    /**
     * Elimina un usuario de un equipo
     */
    public function removeMember($idEquipo, $idUsuario)
    {
        // Obtenemos el usuario y el equipo
        $equipo = Equipo::findOrFail($idEquipo);
        $usuario = User::findOrFail($idUsuario);
        // removemos el usuario del equipo
        $equipo->usuarios()->detach($idUsuario);

        return response()->json(['mensaje' => 'El usuario fue removido del equipo'], 200);
    }


    /**
     * Elimina un usuario de un equipo
     */
    public function updateMember($idEquipo, $idUsuario, $rol)
    {
        // Obtenemos el usuario y el equipo
        $usuario = User::findOrFail($idUsuario);
        $equipo = Equipo::findOrFail($idEquipo);

        // Actualizamos el rol del usuario en el equipo
        $equipo->usuarios()->updateExistingPivot($idUsuario, ['rol' => $rol]);

        return response()->json(['mensaje' => 'El usuario fue actualizado dentro del equipo'], 200);
    }
}
