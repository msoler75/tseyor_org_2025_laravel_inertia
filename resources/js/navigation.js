export default [
    /*{
            title: "Menu 0",
            icon: "ph:shield-chevron",
            description: "description 0",
            url: "/url0",
          },*/

    {
      title: "Menu 1",
      icon: "ph:shield-chevron",
      open: true,
      submenu: {
        header: "esto es un header 1",
        sections: [
          {
            title: "Sección 1.1",
            items: [
              {
                title: "Submenu 1.1.1",
                url: "/url1.1.1",
                description: "Inner Submenu 1.1.1 lorem ipsum",
                icon: "ph:shield-chevron",
              },
              {
                title: "Submenu 1.1.2",
                url: "/url1.1.2",
                description: "Inner Submenu 1.1.2",
                icon: "ph:shield-chevron",
              },
            ],
          },
          {
            title: "Sección 1.2",
            items: [
              {
                title: "Submenu 1.2.1",
                url: "/url1.1.1",
                description: "Inner Submenu 1.2.1 varem suo",
                icon: "ph:shield-chevron",
              },
              {
                title: "Submenu 1.2.2",
                url: "/url1.2.2",
                description: "Inner Submenu 1.2.2 lorem ipsum et quodis",
                icon: "ph:shield-chevron",
              },
            ],
          },
          {
            title: "Sección 1.3",
            items: [
              {
                title: "Submenu 1.3.1",
                url: "/url1.3.1",
                description: "Inner Submenu 1.3.1 fati pelis um tarso doble",
                icon: "ph:shield-chevron",
              },
              {
                title: "Submenu 1.3.2",
                url: "/url1.3.2",
                description: "Inner Submenu 1.3.2",
                icon: "ph:shield-chevron",
              },
            ],
          },
        ],
        footer: "Esto es un footer 1",
      },
    },

    {
      title: "Menu 2",
      icon: "ph:shield-chevron",
      submenu: {
        header: "esto es un header 2",
        sections: [
          {
            title: "Sección 2.1",
            items: [
              {
                title: "Submenu 2.1.1",
                url: "/url2.1.1",
                description: "Inner Submenu 2.1.1 peris quilineoms hum varus",
                icon: "ph:shield-chevron",
              },
              {
                title: "Submenu 2.1.2",
                url: "/url2.1.2",
                description: "Inner Submenu 2.1.2",
                icon: "ph:shield-chevron",
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
