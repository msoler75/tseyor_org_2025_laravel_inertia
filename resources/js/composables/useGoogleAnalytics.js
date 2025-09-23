import { usePage } from '@inertiajs/vue3';

let gtag = null;
let lastPageViewUrl = null;
let lastPageViewTime = 0;

// Definir rangos de tiempo con sus categorías
const timeRanges = [
    { min: 3600, category: '60min' },  // 60+ minutos
    { min: 2700, category: '45min' },  // 45-60 minutos
    { min: 1800, category: '30min' },  // 30-45 minutos
    { min: 900,  category: '15min' },  // 15-30 minutos
    { min: 600,  category: '10min' },  // 10-15 minutos
    { min: 300,  category: '5min' },   // 5-10 minutos
    { min: 120,  category: '2min' },   // 2-5 minutos
    { min: 60,   category: '1min' },   // 1-2 minutos
    { min: 0,    category: '0min' }    // < 1 minuto
]

export function useGoogleAnalytics() {
    const page = usePage();
    const measurementId = computed(() => page.props?.google_analytics?.measurement_id);

    // Función para cargar Google Analytics
    const loadGoogleAnalytics = () => {
        if (!measurementId.value || typeof window === 'undefined') {
            return;
        }

        // Evitar cargar GA múltiples veces
        if (window.gtag) {
            gtag = window.gtag; // Asegurar que la variable local esté sincronizada
            return;
        }

        // Crear y cargar el script de Google Analytics
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId.value}`;
        document.head.appendChild(script);

        // Configurar gtag
        window.dataLayer = window.dataLayer || [];
        window.gtag = function() {
            window.dataLayer.push(arguments);
        };
        gtag = window.gtag;

        // Configuración inicial
        gtag('js', new Date());
        gtag('config', measurementId.value, {
            page_title: document.title,
            page_location: window.location.href,
            // Detectar si está ejecutándose como PWA instalada
            custom_map: {
                'custom_parameter_1': 'pwa_mode'
            }
        });

        // Trackear si está corriendo como PWA
        if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
            gtag('event', 'pwa_install_detected', {
                event_category: 'PWA',
                event_label: 'standalone_mode'
            });
        }
    };

    // Función auxiliar para asegurar que GA esté inicializado
    const ensureGoogleAnalytics = () => {
        if (!gtag && typeof window !== 'undefined' && measurementId.value) {
            // Verificar si window.gtag existe (puede haberse cargado externamente)
            if (window.gtag) {
                gtag = window.gtag;
            } else {
                // Auto-inicializar si es necesario
                loadGoogleAnalytics();
            }
        }
        return !!gtag;
    };

    // Función para enviar eventos de página vista
    const trackPageView = (url = null, title = null) => {
        if (!ensureGoogleAnalytics() || !measurementId.value) return;

        const pageUrl = url || window.location.href;
        const pageTitle = title || document.title;
        const currentTime = Date.now();

        // Evitar tracking duplicado de la misma URL en menos de 1 segundo
        if (lastPageViewUrl === pageUrl && (currentTime - lastPageViewTime) < 1000) {
            console.log('Duplicate page view prevented:', pageUrl);
            return;
        }

        // console.log('trackPageView called with:', pageUrl, pageTitle);

        // Actualizar variables de control
        lastPageViewUrl = pageUrl;
        lastPageViewTime = currentTime;

        // Método recomendado para SPA: usar gtag config para cada page view
        gtag('config', measurementId.value, {
            page_title: pageTitle,
            page_location: pageUrl,
            // Importante para SPAs: enviar page_path relativo
            page_path: new URL(pageUrl).pathname + new URL(pageUrl).search
        });

        // También enviar como evento específico para mayor tracking
        gtag('event', 'page_view', {
            page_title: pageTitle,
            page_location: pageUrl,
            page_path: new URL(pageUrl).pathname + new URL(pageUrl).search
        });
    };

    // Función para enviar eventos personalizados
    const trackEvent = (eventName, parameters = {}) => {
        if (!ensureGoogleAnalytics()) return;

        gtag('event', eventName, parameters);
    };

    // Eventos específicos para el sitio
    const trackDownload = (fileName, fileType = '', fullUrl = '') => {
        console.log('file_download tracked', fileName, fileType, fullUrl);
        trackEvent('file_download', {
            file_name: fileName,
            file_type: fileType,
            file_url: fullUrl,
            page_location: window.location.href,
            page_title: document.title,
        });
    };

    const trackSearch = (searchTerm, searchContext = '') => {
        trackEvent('search', {
            search_term: searchTerm,
            search_context: searchContext,
        });
        console.log('🔍 Búsqueda:', searchTerm, searchContext ? `en ${searchContext}` : '')
    };

    const trackVideoPlay = (videoTitle, videoUrl = '') => {
        trackEvent('video_play', {
            video_title: videoTitle,
            video_url: videoUrl,
        });
    };

    const trackAudioPlay = (audioTitle, audioUrl = '') => {
        trackEvent('audio_play', {
            audio_title: audioTitle,
            audio_url: audioUrl,
        });
    };

    const trackNewsletterSignup = (method = '') => {
        trackEvent('sign_up', {
            method: method,
        });
    };

    const trackContactForm = (formName = 'contact') => {
        if (!gtag) {
            console.warn('⚠️ Google Analytics no disponible, no se puede trackear formulario')
            return
        }

        gtag('event', 'form_submit', {
            form_name: formName,
            page_title: document.title,
            page_location: window.location.href
        })
        console.log('📋 Formulario enviado:', formName)
    }

    const trackUserEngagement = (engagementType, content = '') => {
        if (!gtag) {
            console.warn('⚠️ Google Analytics no disponible, no se puede trackear engagement')
            return
        }

        gtag('event', 'user_engagement', {
            engagement_type: engagementType,
            content: content,
            page_title: document.title,
            page_location: window.location.href
        })
        console.log('👤 Engagement:', engagementType, content)
    }

    const trackDirectAccess = (contentType, contentTitle) => {
        if (!gtag) {
            console.warn('⚠️ Google Analytics no disponible, no se puede trackear acceso directo')
            return 'unknown'
        }

        const referrer = document.referrer
        const currentDomain = window.location.hostname

        // En SPAs con Inertia.js, necesitamos verificar si hay navegación interna previa
        const hasInternalNavigation = window.history.length > 1
        const sessionStart = !window.performance ||
                           window.performance.navigation.type === window.performance.navigation.TYPE_NAVIGATE

        console.log('🔍 Direct Access Debug:', {
            referrer: referrer,
            hasInternalNavigation: hasInternalNavigation,
            sessionStart: sessionStart,
            historyLength: window.history.length,
            navigationType: window.performance ? window.performance.navigation.type : 'unknown'
        })

        // Detectar tipo de acceso
        if (!referrer) {
            // Sin referrer - podría ser directo o navegación SPA
            if (hasInternalNavigation && !sessionStart) {
                // Probablemente navegación interna SPA
                console.log('🔄 Navegación interna SPA detectada')
                return 'internal'
            } else {
                // Sin referrer = acceso directo (URL escrita, marcador, QR, compartido)
                gtag('event', 'direct_access', {
                    content_type: contentType,
                    content_title: contentTitle,
                    access_method: 'direct_url_or_qr',
                    page_title: document.title,
                    page_location: window.location.href,
                    session_start: sessionStart,
                    history_length: window.history.length
                })
                console.log('📱 Acceso directo detectado:', contentType, contentTitle)
                return 'direct'
            }
        } else {
            // Verificar si viene de dominio externo
            try {
                const referrerDomain = new URL(referrer).hostname
                if (referrerDomain !== currentDomain) {
                    // Viene de dominio externo
                    gtag('event', 'external_access', {
                        content_type: contentType,
                        content_title: contentTitle,
                        source_domain: referrerDomain,
                        referrer_url: referrer,
                        page_title: document.title,
                        page_location: window.location.href,
                        session_start: sessionStart,
                        history_length: window.history.length
                    })
                    console.log('🔗 Acceso desde dominio externo:', contentType, contentTitle, 'desde:', referrerDomain)
                    return 'external'
                } else {
                    // Mismo dominio pero con referrer - navegación normal dentro del sitio
                    console.log('🏠 Navegación interna con referrer del mismo dominio')
                    return 'internal'
                }
            } catch (error) {
                // Error al parsear referrer, considerar como directo solo si es inicio de sesión
                if (sessionStart) {
                    gtag('event', 'direct_access', {
                        content_type: contentType,
                        content_title: contentTitle,
                        access_method: 'unknown_referrer',
                        page_title: document.title,
                        page_location: window.location.href,
                        error: error.message
                    })
                    console.log('📱 Acceso con referrer desconocido:', contentType, contentTitle)
                    return 'direct'
                } else {
                    console.log('🔄 Error de referrer en navegación interna')
                    return 'internal'
                }
            }
        }
    }

    const trackViewTime = (contentType, contentTitle, viewTimeSeconds) => {
        if (!ensureGoogleAnalytics()) {
            console.warn('⚠️ Google Analytics no disponible, no se puede trackear tiempo de visualización')
            return
        }

        // Encontrar la categoría correspondiente usando los rangos globales
        const timeCategory = timeRanges.find(range => viewTimeSeconds >= range.min).category

        gtag('event', 'view_time', {
            content_type: contentType,
            content_title: contentTitle,
            view_time_seconds: viewTimeSeconds,
            view_time_minutes: Math.round(viewTimeSeconds / 60 * 10) / 10, // Redondeado a 1 decimal
            time_category: timeCategory,
            page_title: document.title,
            page_location: window.location.href
        })

        console.log('⏱️ Tiempo de visualización:', contentType, Math.round(viewTimeSeconds / 60 * 10) / 10 + 'min', `(${timeCategory})`)
    }

    const trackPlayTime = (mediaType, mediaTitle, playTimeSeconds, totalDurationSeconds = null) => {
        if (!gtag) {
            console.warn('⚠️ Google Analytics no disponible, no se puede trackear tiempo de reproducción')
            return
        }

        // Encontrar la categoría correspondiente usando los rangos globales
        const timeCategory = timeRanges.find(range => playTimeSeconds >= range.min).category

        // Calcular porcentaje de reproducción si se proporciona la duración total
        let playProgress = 'unknown'
        if (totalDurationSeconds && totalDurationSeconds > 0) {
            const progressPercentage = Math.round((playTimeSeconds / totalDurationSeconds) * 100)
            playProgress = `${Math.min(progressPercentage, 100)}%`
        }

        gtag('event', 'media_play_time', {
            media_type: mediaType, // 'audio' o 'video'
            media_title: mediaTitle,
            play_time_seconds: playTimeSeconds,
            play_time_minutes: Math.round(playTimeSeconds / 60 * 10) / 10,
            time_category: timeCategory,
            play_progress: playProgress,
            total_duration_seconds: totalDurationSeconds,
            page_title: document.title,
            page_location: window.location.href
        })

        console.log('🎵 Tiempo de reproducción:', mediaType, Math.round(playTimeSeconds / 60 * 10) / 10 + 'min', `(${timeCategory})`, playProgress !== 'unknown' ? `- ${playProgress}` : '')
    }

    // Función para configurar el consentimiento de cookies (GDPR)
    const grantConsent = () => {
        if (!gtag) return;

        gtag('consent', 'update', {
            'analytics_storage': 'granted'
        });
    };

    const denyConsent = () => {
        if (!gtag) return;

        gtag('consent', 'update', {
            'analytics_storage': 'denied'
        });
    };

    return {
        loadGoogleAnalytics,
        ensureGoogleAnalytics,
        trackPageView,
        trackEvent,
        trackDownload,
        trackSearch,
        trackVideoPlay,
        trackAudioPlay,
        trackNewsletterSignup,
        trackContactForm,
        trackUserEngagement,
        trackDirectAccess,
        trackViewTime,
        trackPlayTime,
        grantConsent,
        denyConsent,
        measurementId
    };
}
