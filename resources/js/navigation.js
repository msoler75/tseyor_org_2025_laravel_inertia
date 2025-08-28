import ApplicationMark from "@/Components/ApplicationMark.vue";
import { markRaw } from "vue";

const APP_PUZLE_URL = "https://puzle.tseyor.org/";
const APP_MUULAR_URL = "/muular-electronico";

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
    title: "Novedades",
    icon: "ph:clock-counter-clockwise-duotone",
    description: "Novedades",
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
              description: "Los contenidos de Tseyor más recientes",
              icon: "ph:clock-counter-clockwise-duotone",
            },
            /*{
                class:"pointer-events-none opacity-0",
             title: "Boletines",
              route: "boletines",
              description: "Boletines de la comunidad Tseyor",
              icon: "ph:newspaper-clipping-duotone",
            },*/
            {
              title: "Boletines",
              route: "boletines",
              description: "Boletines de la comunidad Tseyor",
              icon: "ph:newspaper-clipping-duotone",
            },
          ],
        },
        {
          title: "Noticias y Eventos",
          items: [
            {
              title: "Noticias",
              route: "noticias",
              description: "Noticias y anuncios de la comunidad Tseyor",
              icon: "ph:megaphone-simple-duotone",
            },
            {
              title: "Eventos",
              route: "eventos",
              description: "Cursos, convivencias y encuentros",
              icon: "ph:calendar-duotone",
            },
          ],
        },
        {
          title: "Redes",
          class: "flex flex-col gap-1",
          // index: 2,
          items: [
            {
              title: "Facebook",
              url: "http://facebook.com/tseyor",
              external: true,
              target: "_blank",
              icon: "ph:facebook-logo-duotone",
              class: "text-xs",
            },
            {
              title: "X",
              url: "http://twitter.com/tseyor",
              external: true,
              target: "_blank",
              icon: "bi:twitter-x",
              class: "text-xs",
            },
            {
              title: "Youtube",
              url: "http://youtube.com/@tseyor",
              external: true,
              target: "_blank",
              icon: "bi:youtube",
              class: "text-xs",
            },
          ],
        },
      ],
    },
  },

  {
    title: "Biblioteca",
    description: "Comunicados, libros, artículos, noticias...",
    icon: "ph:article-duotone",
    route: "biblioteca",
    open: false,
    submenu: {
      // header: "Novedades",
      sections: [
        {
          title: "Biblioteca",
          items: [
            {
              title: "Biblioteca Tseyor",
              route: "biblioteca",
              description: "Información sobre la Biblioteca Tseyor",
              icon: "ph:books-duotone",
            },
          ],
        },
        {
          title: "Blogs",
          class: "flex flex-col gap-1",
          index: 0,
          items: [
            {
              title: "Blog",
              route: "blog",
              description: "Artículos de nuestros blog",
              icon: "ph:pencil-line-duotone",
            },
          ],
        },
        {
          title: "Descubre",
          class: "flex flex-col gap-1",
          index: 0,
          items: [
            {
              title: "Descubre",
              route: "descubre",
              description: "Presentación de algunos temas clave de Tseyor",
              icon: "ph:lightbulb-duotone",
            },
          ],
        },
        {
          title: "Documentos",
          items: [
            {
              title: "Comunicados",
              route: "comunicados",
              description: "Comunicados recibidos de las estrellas",
              icon: "ph:flying-saucer-duotone",
            },
            {
              title: "Libros",
              route: "libros",
              description: "Todos los temas de la filosofía Tseyor",
              icon: "ph:book-bookmark-duotone",
            },
            {
              title: "Meditaciones",
              route: "meditaciones",
              description: "Meditaciones para el trabajo interior",
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
              description: "Meditaciones, talleres, cuentos, canciones...",
              icon: "ph:music-notes-duotone",
            },
            {
              title: "Radio Tseyor",
              route: "radio",
              description: "Escucha nuestra radio online 24/7",
              icon: "ph:radio-duotone",
            },
            {
              title: "Vídeos",
              route: "videos",
              description: "Meditaciones, talleres, cuentos, canciones...",
              icon: "ph:youtube-logo-duotone",
              // class: "text-xs",
            },
            {
              title: "Psicografías",
              route: "psicografias",
              description: "Psicografías para el trabajo de abstracción",
              icon: "ph:image-duotone",
              // class: "text-xs",
            },
          ],
        },
      ],
      // footer: "Esto es un footer 1",
    },
  },

  {
    title: "Formación",
    icon: "ph:ph:graduation-cap-duotone",
    submenu: {
      // header: "esto es un header 2",
      sections: [
        {
          title: "Cursos",
          items: [
            {
              title: "Curso Holístico Tseyor",
              route: "cursos",
              description: "Conoce nuestro curso de origen estelar",
              icon: "ph:chalkboard-teacher-duotone",
            },
            {
              title: "Inscríbete a nuestro curso",
              route: "cursos.inscripcion.nueva",
              description: "Aprende gratis nuestra filosofía",
              icon: "ph:note-pencil-duotone",
            },
          ],
        },

        {
          title: "Consulta",
          items: [
            {
              title: "Glosario de términos",
              route: "terminos",
              description: "Índice de términos del conocimiento de Tseyor",
              icon: "ph:text-a-underline-duotone",
            },
            {
              title: "Guías Estelares",
              route: "guias",
              description: "Conoce a los tutores de la Confederación",
              icon: "ph:user-gear-duotone",
            },
            {
              title: "Preguntas frecuentes",
              route: "preguntas",
              description: "Preguntas más habituales y su respuesta",
              icon: "ph:question-duotone",
            },
          ],
        },

        {
          title: "Orientación",
          items: [
            {
              title: "Mis primeros pasos",
              url: "/mis-primeros-pasos",
              description: "Dónde comenzar en la comunidad Tseyor",
              icon: "ph:list-numbers-duotone",
            },
            {
              title: "Tutoriales",
              route: "tutoriales",
              description: "Tutoriales de uso de las herramientas",
              icon: "ph:steps-duotone",
            },
          ],
        },
      ],
    },
  },

  {
    title: "Organización",
    icon: "ph:shield-cheph:tree-duotone",
    submenu: {
      // header: "esto es un header 2",
      sections: [
        {
          title: "Presentación",
          items: [
            {
              title: "¿Quiénes somos?",
              route: "quienes-somos",
              description: "Descubre quiénes somos y cómo podemos ayudarnos",
              icon: "ph:users-three-duotone",
            },
            {
              title: "Nuestra Filosofía",
              route: "filosofia",
              description: "Conoce la filosofía cósmico crística",
              icon: "ph:fish-simple-duotone",
            },
            {
              title: "Orígenes de Tseyor",
              route: "origenes-de-tseyor",
              description: "El orígen del contacto",
              icon: "ph:shooting-star-duotone",
            },
          ],
        },
        {
          title: "Lugares Tseyor",
          items: [
            {
              title: "Dónde estamos",
              route: "contactos",
              description: "Todas las ubicaciones donde encontrar Tseyor",
              icon: "ph:map-pin-line-duotone",
            },
            {
              title: "Centros Tseyor",
              route: "centros",
              description: "Casas Tseyor y Muulasterios en el mundo",
              icon: "ph:house-line-duotone",
            },
            {
              title: "Contactar",
              route: "contactar",
              description: "Contacta con nosotros",
              icon: "ph:envelope-duotone",
            },
          ],
        },

        {
          title: "Estructura",
          items: [
            {
              title: "Asociación Tseyor",
              route: "asociacion",
              description: "Conoce la primera entidad de Tseyor",
              icon: "ph:tree-duotone",
            },
            {
              title: "Universidad Tseyor de Granada",
              route: "utg",
              description: "Conoce nuestra Universidad",
              icon: "ph:student-duotone",
            },
            {
              title: "ONG Mundo Armónico Tseyor",
              route: "ong",
              description: "Conoce nuestra ONG y sus objetivos",
              icon: "ph:handshake-duotone",
            },
          ],
        },
      ],
      // footer: "Esto es un footer 2",
    },
  },

  {
    title: "Comunidad",
    icon: "ph:globe-duotone",
    submenu: {
      header: "Para miembros de Tseyor",
      sections: [
        {
          title: "Equipos y usuarios",
          items: [
            {
              title: "Equipos",
              route: "equipos",
              description: "Equipos de trabajo",
              icon: "ph:users-four-duotone",
            },
            {
              title: "Usuarios",
              route: "usuarios",
              description: "Listado de Usuarios",
              icon: "ph:users-duotone",
            },
            {
              title: "Informes",
              route: "informes",
              description: "Informes de los equipos",
              icon: "ph:files-duotone",
            },
            {
              title: "Publicaciones",
              route: "publicaciones",
              description: "Publicaciones de los miembros de la comunidad",
              icon: "ph:flower-duotone",
              disabled: true,
            },
          ],
        },

        {
          title: "Documentos y archivos",
          items: [
            {
              title: "Normativas",
              route: "normativas",
              description: "Estatutos, protocolos y normativas",
              icon: "ph:hand-palm-duotone",
            },
            {
              title: "Archivos",
              route: "archivos0",
              description: "Todos los archivos y documentos en carpetas",
              icon: "ph:archive-box-duotone",
            },

            {
              title: "Experiencias",
              description: "Experiencias interdimensionales",
              route: "experiencias",
              icon: "ph:butterfly-duotone",
            },
          ],
        },
        {
          title: "Herramientas",
          items: [
            {
              title: "Muular Electrónico",
              url: APP_MUULAR_URL,
              external: true,
              target: "_self",
              description: "Intercambio de bienes y servicios",
              icon: "ph:swap-duotone",
              disabled: false,
            },
            {
              title: "Sello de Tseyor",
              route: "sello",
              description: "Meditación con el sello de Tseyor",
              component: markRaw(ApplicationMark),
              disabled: false,
            },
            {
              title: "Juego del puzle",
              //obtiene de la base del dominio de la app, y le agrega el subdominio puzle.
              url: APP_PUZLE_URL,
              external: true,
              target: "_blank",
              description: "Juego del puzle con las láminas de abstacción",
              icon: "ph:file-text-duotone",
              disabled: false,
            },
            {
              title: "Cartas para el autodescubrimiento",
              route: "equipos",
              description: "Meditaciones para el trabajo interior",
              icon: "ph:file-text-duotone",
              disabled: true,
            },
          ],
        },
      ],
    },
  },
];
