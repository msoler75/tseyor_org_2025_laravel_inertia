export default [
    {
            title: "Novedades",
            icon: "ph:clock-counter-clockwise-duotone",
            description: "Novedades",
            route: 'novedades',
          },

    {
      title: "Contenidos",
      description: "Comunicados, libros, artículos, noticias...",
      icon: "ph:article-duotone",
      open: false,
      submenu: {
        // header: "Novedades",
        sections: [


          {
            title: "Documentos",
            items: [
                {
                    title: "Comunicados",
                    route: 'comunicados',
                    description: "Comunicados recibidos de las estrellas",
                    icon: "ph:star-four-duotone",
                  },
              {
                title: "Libros",
                route: 'libros',
                description: "Todos los temas de la filosofía Tseyor",
                icon: "ph:book-bookmark-duotone",
              },
              {
                title: "Blog",
                route: 'entradas',
                description: "Artículos de nuestros blog",
                icon: "ph:pencil-line-duotone",
              },
            ],
          },
          {
            title: "Noticias y Eventos",
            items: [
                {
                    title: "Noticias",
                    route: 'noticias',
                    description: "Noticias y anuncios de la comunidad Tseyor",
                    icon: "ph:megaphone-simple-duotone",
                    class: 'border-b'
                  },
              {
                title: "Eventos",
                route: 'eventos',
                description: "Cursos, convivencias y encuentros",
                icon: "ph:calendar-duotone",
              }
            ],
          },
          {
            title: "Media",
            items: [
                {
                    title: "Audios",
                    route: 'audios',
                    description: "Meditaciones, cuentos, canciones...",
                    icon: "ph:music-notes-duotone",
                  },
                  {
                    title: "Vídeos",
                    route: 'videos',
                    description: "Meditaciones, cuentos, canciones...",
                    icon: "ph:youtube-logo-duotone",
                  }
            ],
          }

        ],
        footer: "Esto es un footer 1",
      },
    },

    {
      title: "Organización",
      icon: "ph:shield-cheph:tree-duotone",
      submenu: {
        // header: "esto es un header 2",
        sections: [
          {
            title: "Lugares Tseyor",
            items: [
              {
                title: "Dónde estamos",
                route: 'contactos',
                description: "Todas las ubicaciones donde encontrar Tseyor",
                icon: "ph:map-pin-line-duotone",
              },
              {
                title: "Centros Tseyor",
                route: 'centros',
                description: "Centros Tseyor en el mundo",
                icon: "ph:lighthouse-duotone",
              }
            ],
          },
          {
            title: "Presentación",
            items: [
              {
                title: "¿Quiénes somos?",
                route: 'quienes-somos',
                description: "Conoce nuestra historia y valores",
                icon: "ph:users-three-duotone",
              }
            ],
          },
          {
            title: "Cursos",
            items: [
              {
                title: "Curso Holístico Tseyor",
                route: 'cursos',
                description: "Conoce nuestro curso de origen estelar",
                icon: "ph:chalkboard-teacher-duotone",
              },
              {
                title: "Inscríbete a nuestro curso",
                route: 'cursos.inscripcion',
                description: "Aprende gratis nuestra filosofía",
                icon: "ph:note-pencil-duotone",
              }

            ],
          },
        ],
        footer: "Esto es un footer 2",
      },
    },
  ]
