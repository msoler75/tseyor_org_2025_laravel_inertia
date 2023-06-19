export default [
    {
            title: "Novedades",
            icon: "vscode-icons:file-type-bolt",
            description: "Novedades",
            url: "/novedades",
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
            title: "Noticias y Eventos",
            items: [
                {
                    title: "Noticias",
                    url: "/noticias",
                    description: "Noticias y anuncios de la comunidad Tseyor",
                    icon: "ph:megaphone-simple-duotone",
                  },
              {
                title: "Eventos",
                url: "/eventos",
                description: "Cursos, convivencias y encuentros",
                icon: "ph:calendar-duotone",
              }
            ],
          },
          {
            title: "Documentos",
            items: [
                {
                    title: "Comunicados",
                    url: "/comunicados",
                    description: "Comunicados recibidos de las estrellas",
                    icon: "ph:star-four-duotone",
                  },
              {
                title: "Libros",
                url: "/libros",
                description: "Todos los temas de la filosofía Tseyor",
                icon: "ph:book-bookmark-duotone",
              },
              {
                title: "Blog",
                url: "/entradas",
                description: "Artículos de nuestros blog",
                icon: "ph:pencil-line-duotone",
              },
            ],
          },
          {
            title: "Media",
            items: [
                {
                    title: "Audios",
                    url: "/audios",
                    description: "Meditaciones, cuentos, canciones...",
                    icon: "ph:music-notes-duotone",
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
            title: "Equipos",
            items: [
              {
                title: "Centros",
                url: "/centros",
                description: "Centros Tseyor en el mundo",
                icon: "ph:lighthouse-duotone",
              },
              {
                title: "Contactos",
                url: "/contactos",
                description: "Todas las ubicaciones donde encontrar Tseyor",
                icon: "ph:map-pin-line-duotone",
              },
            ],
          },
          {
            title: "Sección 2.2",
            items: [
              {
                title: "Submenu 2.2.1",
                url: "/url2.1.1",
                description: "Inner Submenu 2.2.1",
                icon: "ph:shield-chevron",
              },
              {
                title: "Submenu 2.2.2",
                url: "/url2.2.2",
                description: "Inner Submenu 2.2.2",
                icon: "ph:shield-chevron",
              },
            ],
          },
          {
            title: "Sección 2.3",
            items: [
              {
                title: "Submenu 2.3.1",
                url: "/url2.3.1",
                description: "Inner Submenu 2.3.1",
                icon: "ph:shield-chevron",
              },
              {
                title: "Submenu 2.3.2",
                url: "/url2.3.2",
                description: "Inner Submenu 2.3.2",
                icon: "ph:shield-chevron",
              },
            ],
          },
        ],
        footer: "Esto es un footer 2",
      },
    },
  ]
