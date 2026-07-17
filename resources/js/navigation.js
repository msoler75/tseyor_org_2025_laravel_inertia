import ApplicationMark from "@/Components/ApplicationMark.vue";
import { markRaw } from "vue";

const APP_PUZLE_URL = "https://puzle.tseyor.org/";
const APP_MUULAR_URL_REDIRECT = "/muular-electronico";
const APP_TSEYOR_CANVA_URL = "/tseyor-canva";

export default [
  {
    title: "Inicio",
    icon: "ph:house-simple-duotone",
    description: "Portada de la web",
    url: "/",
    onlyAside: true,
    open: false,
    submenu: null,
  },
  {
    title: "Curso",
    icon: "ph:graduation-cap-duotone",
    route: "cursos",
    open: false,
    submenu: {
      sections: [
        {
          title: "El curso",
          items: [
            {
              title: "Curso Holístico Tseyor",
              route: "cursos",
              description: "Aprende gratis la base de la filosofía de las estrellas, con acompañamiento y sin dogmas.",
              icon: "ph:chalkboard-teacher-duotone",
            },
            {
              title: "Inscríbete gratis",
              route: "cursos.inscripcion.nueva",
              description: "Comienza el curso online y explora sin dogmas.",
              icon: "ph:note-pencil-duotone",
            },
          ],
        },
        {
          title: "Consulta",
          items: [
            {
              title: "Mis primeros pasos",
              url: "/mis-primeros-pasos",
              description: "Una guía para empezar a explorar TSEYOR con claridad.",
              icon: "ph:list-numbers-duotone",
            },
            {
              title: "Preguntas frecuentes",
              route: "preguntas",
              description: "Resuelve dudas habituales antes de empezar.",
              icon: "ph:question-duotone",
            },
            {
              title: "Glosario",
              route: "terminos",
              description: "Consulta términos clave de la filosofía TSEYOR.",
              icon: "ph:text-a-underline-duotone",
            },
          ],
        },
      ],
    },
  },
  {
    title: "Biblioteca",
    icon: "ph:books-duotone",
    route: "biblioteca",
    open: false,
    submenu: {
      sections: [
        {
          title: "Biblioteca",
          items: [
            {
              title: "Biblioteca Tseyor",
              route: "biblioteca",
              description: "Libros, comunicados, audios y materiales de libre descarga.",
              icon: "ph:books-duotone",
            },
          ],
        },
        {
            title: "Comunidad",
            index:0,
            items: [
             {
              title: "Galerías",
              route: "galerias",
              description: "Fotografías, arte y memoria visual de la comunidad.",
              icon: "ph:image-duotone",
            }]
        },
        {
          title: "Documentos",
          items: [
            {
              title: "Comunicados",
              route: "comunicados",
              description: "Comunicados recibidos de los Guías Estelares y la Confederación.",
              icon: "ph:flying-saucer-duotone",
            },
            {
              title: "Libros",
              route: "libros",
              description: "Libros y monografías sobre la filosofía TSEYOR.",
              icon: "ph:book-bookmark-duotone",
            },
            {
              title: "Meditaciones",
              route: "meditaciones",
              description: "Prácticas para el trabajo interior.",
              icon: "ph:file-text-duotone",
            },
          ],
        },
        {
          title: "Media",
          items: [
            {
              title: "Audios",
              route: "audios",
              description: "Meditaciones, talleres, cuentos, canciones y materiales sonoros.",
              icon: "ph:music-notes-duotone",
            },
            {
              title: "Radio Tseyor",
              route: "radio",
              description: "Escucha la radio online de TSEYOR.",
              icon: "ph:radio-duotone",
            },
            {
              title: "Vídeos",
              route: "videos",
              description: "Material audiovisual de meditaciones, talleres y encuentros.",
              icon: "ph:youtube-logo-duotone",
            },
            {
              title: "Psicografías",
              route: "psicografias",
              description: "Láminas para el trabajo de abstracción.",
              icon: "ph:image-duotone",
            },
          ],
        },

      ],
    },
  },
  {
    title: "Filosofía",
    icon: "ph:fish-simple-duotone",
    route: "filosofia",
    open: false,
    submenu: {
      sections: [
        {
          title: "Filosofía",
          items: [
            {
               title: "Nuestra filosofía",
               route: "filosofia",
               description: "Filosofía de las estrellas, conciencia, autodescubrimiento y Sociedades Armónicas.",
               icon: "ph:fish-simple-duotone",
             },
             {
               title: "Todos los temas",
               route: "filosofia.temas",
               description: "Explora todos los temas de la filosofía TSEYOR organizados por categoría.",
               icon: "ph:list-dashes-duotone",
             },
             {
               title: "Guías Estelares",
              route: "guias",
              description: "Conoce a los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia.",
              icon: "ph:user-gear-duotone",
            },
            {
              title: "Orígenes de TSEYOR",
              route: "origenes-de-tseyor",
              description: "El origen del contacto y la historia del grupo.",
              icon: "ph:shooting-star-duotone",
            },
          ],
        },
        {
          title: "Temas clave de TSEYOR",
          items: [
             {
              title: "El Rayo Sincronizador",
              url: "/el-rayo-sincronizador",
              description: "El rayo que marca el cambio de ciclo cósmico.",
              icon: "ph:lightbulb-duotone",
            },
             {
              title: "Sociedades Armónicas",
              url: "/las-sociedades-armonicas",
              description: "El modelo de convivencia fraternal hacia el que caminamos.",
              icon: "ph:handshake-duotone",
            },
            {
              title: "Especialización",
              url: "/especializacion",
              description: "Descubre tu talento único al servicio de la comunidad.",
              icon: "ph:gear-six-duotone",
            },
            {
              title: "Retroalimentación",
              url: "/retroalimentacion",
              description: "El aprendizaje colectivo a través del grupo.",
              icon: "ph:arrows-counter-clockwise-duotone",
            },
            {
              title: "Espejos",
              url: "/espejos",
              description: "El principio del espejo y el autoconocimiento.",
              icon: "ph:eye-duotone",
            }
          ],
        },
      ],
    },
  },
  {
    title: "Quiénes somos",
    icon: "ph:users-three-duotone",
    route: "quienes-somos",
    open: false,
    submenu: {
      sections: [
        {
          title: "Presentación",
          items: [
            {
              title: "Quiénes somos",
              route: "quienes-somos",
              description: "Conoce la comunidad TSEYOR, su origen, propósito y forma de trabajar.",
              icon: "ph:users-three-duotone",
            },
            {
              title: "ONG Mundo Armónico TSEYOR",
              route: "ong",
              description: "Nuestra ONG sin ánimo de lucro y sus objetivos.",
              icon: "ph:handshake-duotone",
            },
            {
              title: "Asociación TSEYOR",
              route: "asociacion",
              description: "Conoce la primera entidad de TSEYOR.",
              icon: "ph:tree-duotone",
            },
            {
              title: "Universidad TSEYOR de Granada",
              route: "utg",
              description: "Conoce nuestra Universidad.",
              icon: "ph:student-duotone",
            },
          ],
        },
        {
          title: "Lugares Tseyor",
          items: [
            {
              title: "Dónde estamos",
              route: "contactos",
              description: "Ubicaciones y contactos donde encontrar TSEYOR.",
              icon: "ph:map-pin-line-duotone",
            },
            {
              title: "Centros TSEYOR",
              route: "centros",
              description: "Casas TSEYOR y Muulasterios en el mundo.",
              icon: "ph:house-line-duotone",
            },
            {
              title: "Contactar",
              route: "contactar",
              description: "Ponte en contacto con nosotros.",
              icon: "ph:envelope-duotone",
            },
          ],
        },
      ],
    },
  },
  {
    title: "Novedades",
    icon: "ph:clock-counter-clockwise-duotone",
    route: "novedades",
    open: false,
    submenu: {
      sections: [
        {
          title: "Novedades",
          items: [
            {
              title: "Novedades",
              route: "novedades",
              description: "Los contenidos más recientes de TSEYOR.",
              icon: "ph:clock-counter-clockwise-duotone",
            },
            {
              title: "Eventos",
              route: "eventos",
              description: "Cursos, convivencias y encuentros próximos.",
              icon: "ph:calendar-duotone",
            },
            {
              title: "Noticias",
              route: "noticias",
              description: "Noticias y anuncios de la comunidad TSEYOR.",
              icon: "ph:megaphone-simple-duotone",
            },
            {
              title: "Boletines",
              route: "boletines",
              description: "Boletines mensuales de la comunidad TSEYOR.",
              icon: "ph:newspaper-clipping-duotone",
            },
          ],
        },
        {
          title: "Redes",
          items: [
            {
              title: "Facebook",
              url: "http://facebook.com/tseyor",
              external: true,
              target: "_blank",
              icon: "ph:facebook-logo-duotone",
            },
            {
              title: "X",
              url: "http://twitter.com/tseyor",
              external: true,
              target: "_blank",
              icon: "bi:twitter-x",
            },
            {
              title: "Youtube",
              url: "http://youtube.com/@tseyor",
              external: true,
              target: "_blank",
              icon: "bi:youtube",
            },
          ],
        },
      ],
    },
  },
    {
    title: "Blog",
    icon: "ph:pencil-line-duotone",
    route: "blog",
    open: false,
    /*submenu: {
      sections: [
        {
          title: "Blog",
          items: [
            {
              title: "Blog",
              route: "blog",
              description: "Vivencias, encuentros, imágenes y vida de la comunidad TSEYOR.",
              icon: "ph:pencil-line-duotone",
            },
          ],
        },
      ],
    },*/
  },
  {
    title: "Miembros",
    icon: "ph:globe-duotone",
    open: false,
    submenu: {
      header: "Para miembros de TSEYOR",
      sections: [
        {
          title: "Equipos",
          items: [
            {
              title: "Equipos",
              route: "equipos",
              description: "Equipos de trabajo.",
              icon: "ph:users-four-duotone",
            },
            {
              title: "Usuarios",
              route: "usuarios",
              description: "Listado de usuarios.",
              icon: "ph:users-duotone",
            },
            {
              title: "Informes",
              route: "informes",
              description: "Informes de los equipos.",
              icon: "ph:files-duotone",
            },
            {
              title: "Salas",
              route: "salas",
              description: "Salas virtuales de reuniones.",
              icon: "ph:chat-teardrop-text-duotone",
            },
          ],
        },
        {
          title: "Documentación y comunidad",
          items: [
            {
              title: "Normativas",
              route: "normativas",
              description: "Estatutos, protocolos y normativas.",
              icon: "ph:hand-palm-duotone",
            },
            {
              title: "Archivos",
              route: "archivos0",
              description: "Archivos y documentos en carpetas.",
              icon: "ph:archive-box-duotone",
            },
            {
              title: "Experiencias",
              route: "experiencias",
              description: "Experiencias compartidas por miembros y participantes.",
              icon: "ph:butterfly-duotone",
            },
            {
              title: "Publicaciones",
              route: "publicaciones",
              description: "Publicaciones de los miembros de la comunidad.",
              icon: "ph:flower-duotone",
              disabled: true,
            },
          ],
        },
        {
          title: "Herramientas",
          items: [
            {
              title: "Muular Electrónico",
              url: APP_MUULAR_URL_REDIRECT,
              external: true,
              target: "_blank",
              rel: "noopener noreferrer",
              description: "Intercambio de bienes y servicios.",
              icon: "ph:swap-duotone",
            },
            {
              title: "Sello de TSEYOR",
              route: "sello",
              description: "Meditación con el sello de TSEYOR.",
              component: markRaw(ApplicationMark),
            },
            {
              title: "Juego del puzle",
              url: APP_PUZLE_URL,
              external: true,
              target: "_blank",
              description: "Juego del puzle con las láminas de abstracción.",
              icon: "ph:puzzle-piece-duotone",
            },
            {
              title: "TSEYOR Canva",
              url: APP_TSEYOR_CANVA_URL,
              external: true,
              target: "_blank",
              rel: "noopener noreferrer",
              description: "Aplicación para el diseño de carteles o anuncios.",
              icon: "ph:paint-brush-duotone",
              disabled: true,
            },
          ],
        },
      ],
    },
  },
];
