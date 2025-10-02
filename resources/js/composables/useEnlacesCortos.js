import { getMyDomain } from './srcutils.js'

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







  /**
   * Detecta si una URL es un enlace corto
   * Los enlaces cortos tienen prefijos: /d/ (documentos), /a/ (audio), /e/ (eventos/general)
   *
   * @param {string} href - URL a verificar (puede ser path relativo o URL completa)
   * @returns {boolean} - true si es un enlace corto
   *
   * @example
   * esEnlaceCorto('/d/abc123') // true
   * esEnlaceCorto('https://tseyor.org/d/abc123') // true
   * esEnlaceCorto('https://otro-sitio.com/d/abc123') // false
   * esEnlaceCorto('/eventos/encuentro') // false
   */
  const esEnlaceCorto = (href) => {
    if (!href) return false

    let path = href

    // Si es una URL completa, verificar que sea de nuestro dominio
    try {
      if (href.match(/^https?:\/\//)) {
        const url = new URL(href)
        const miDominio = getMyDomain()

        // Si el origen no coincide, no es un enlace corto nuestro
        if (url.origin !== miDominio) {
          return false
        }

        path = url.pathname
      }
    } catch (e) {
      // Si falla el parsing, usar la URL original
      path = href
    }

    // Detectar enlaces cortos por el patrón del path: /d/..., /a/..., /e/...
    return /^\/[dae]\/[a-zA-Z0-9]+/.test(path)
  }

  return {
    // Función principal - la única que necesitas
    obtenerEnlaceCorto,

    // API de bajo nivel (para casos avanzados con metadatos específicos)
    crear,

    // Utilidad para detectar enlaces cortos
    esEnlaceCorto
  }
}

export default useEnlacesCortos
