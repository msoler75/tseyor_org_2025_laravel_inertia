<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comentario;

class ComentariosController extends Controller
{
    public function index(Request $request)
    {
        $cid = $request->input('contenido_id') ?? "";

        if (!$cid)
            return response()->json([
                'error' => 'debe especificar contenido_id'
            ], 400);

        $comentarios = Comentario::join('users', 'comentarios.user_id', '=', 'users.id')
            ->where('contenido_id', $cid)
            ->select('comentarios.*', 'users.name as user_name', 'users.profile_photo_path as user_photo')
            ->get()
            ->toArray();

        foreach ($comentarios as $idx => $comment) {
            $comentarios[$idx]['autor'] = [
                'id' => $comment['user_id'],
                'nombre' => $comment['user_name'],
                'imagen' => $comment['user_photo']
            ];
            unset($comentarios[$idx]['contenido_id']);
            unset($comentarios[$idx]['user_id']);
            unset($comentarios[$idx]['user_name']);
            unset($comentarios[$idx]['user_photo']);
            // unset($comentarios[$idx]['author']);
        }

        return response()->json([
            'comentarios' => $comentarios
        ], 200);
    }

    public function create(Request $request)
    {
        // ...
    }
}

/*


  const commentsData = [
    {
      id: 1,
      author: {id: 1, name:'Ana', avatar:'https://via.placeholder.com/480x480.png/00ee33'},
      date: 1631299213,
      content: '¡Gran artículo!',
      replies: [
        {
          id: 2,
          author: {id: 2, name:'Jorge', avatar:'https://via.placeholder.com/480x480.png/ffee33'},
          date: 1631492213,
          content: 'Gracias por compartir',
          replies: []
        },
        {
          id: 3,
          author: {id: 1, name:'Ana', avatar:'https://via.placeholder.com/480x480.png/00ee33'},
          date: 1674699213,
          content: 'Estoy de acuerdo contigo',
          replies: [
            {
              id: 4,
              author: {id: 3, name:'Jaime', avatar:'https://via.placeholder.com/480x480.png/040ef3'},
              date: 1681299213,
              content: '¡Me alegra que estés de acuerdo!',
              replies: []
            }
          ]
        }
      ]
    },
    {
      id: 5,
      author: {id: 1, name:'Ana', avatar:'https://via.placeholder.com/480x480.png/00ee33'},
      date: 1631299213,
      content: 'Ah!, y añadir: qué interesante perspectiva',
      replies: []
    }
  ];

*/
