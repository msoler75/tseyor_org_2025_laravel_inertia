/**
 * Composable para gestionar enlaces cortos
 */
export function useEnlacesCortos() {

  /**
   * Crear un enlace corto
   */
  const crear = async (datos) => {
    try {
      const response = await fetch('/enlaces-cortos', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(datos)
      })

      if (!response.ok) {
        throw new Error('Error al crear enlace corto')
      }

      return await response.json()
    } catch (error) {
      console.error('Error creando enlace corto:', error)
      throw error
    }
  }

  /**
   * Obtener enlace corto para una URL (crea si no existe)
   * Detecta automáticamente el prefijo basado en la extensión del archivo
   */
  const obtenerEnlaceCorto = async (url) => {

    try {
      const response = await fetch('/obtener-enlace', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({ url })
      })

      if (!response.ok) {
        throw new Error('Error al obtener enlace corto')
      }

      const resultado = await response.json()

      if (resultado.url_corta) {
        const esNuevo = resultado.creado_ahora || false
        console.log(esNuevo ? '✅ Enlace creado:' : '✅ Enlace existente:', resultado.url_corta)
        return resultado.url_corta
      }

      // Si no se acortó (no cumple umbral), devolver URL original
      return url

    } catch (error) {
      console.warn('Error obteniendo enlace corto, usando URL original:', error)
      return url
    }
  }







  return {
    // Función principal - la única que necesitas
    obtenerEnlaceCorto,

    // API de bajo nivel (para casos avanzados con metadatos específicos)
    crear
  }
}

export default useEnlacesCortos
