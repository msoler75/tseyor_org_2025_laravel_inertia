// to-do: import { useMutationObserver } from '@vueuse/core'

export const onThemeChange = () => {
  var callbacks = [];

  function to(fn) {
    callbacks.push(fn);
  }


  if (!window.themeAdapterInstalled) {
    // Selecciona el elemento que deseas observar
    const target = document.documentElement;

    // Crea una instancia del Mutation Observer
    const observer = new MutationObserver((mutationsList) => {
      for (const mutation of mutationsList) {
        if (mutation.type === "attributes") {
          // Se ha producido un cambio en un atributo
          console.log('theme changed!')

          // Obtén el nombre del atributo modificado
          const attributeName = mutation.attributeName;

          // Obtén el valor actual del atributo
          const attributeValue = target.getAttribute(attributeName);

          if (attributeName == "data-bs-theme") {
            for (const c of callbacks) c(attributeValue);
          }

          console.log(
            `El atributo "${attributeName}" ha cambiado. Nuevo valor: "${attributeValue}"`
          );
        }
      }
    });

    // Opciones de configuración para el Mutation Observer
    const config = {
      attributes: true, // Observar cambios en los atributos
      attributeOldValue: true, // Incluir el valor anterior del atributo en las mutaciones
    };

    // Inicia la observación del elemento objetivo con las opciones de configuración
    observer.observe(target, config);

    window.themeAdapterInstalled = true;
  }

  return { to};
};

export const    currentTheme = ()=> {
    return document.documentElement.getAttribute("data-bs-theme")
  }




  export const updateTheme = () => {

    const globalTheme = currentTheme()

    const theme =  globalTheme== 'dark' ? 'winter' : ''

    document.querySelector("body").setAttribute("data-theme", theme)


  }
