import { usePage } from '@inertiajs/vue3';

let gtag = null;
let lastPageViewUrl = null;
let lastPageViewTime = 0;

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

    // Función para enviar eventos de página vista
    const trackPageView = (url = null, title = null) => {
        if (!gtag || !measurementId.value) return;

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
        if (!gtag) return;

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
        gtag('event', 'form_submit', {
            form_name: formName,
            page_title: document.title,
            page_location: window.location.href
        })
        console.log('📋 Formulario enviado:', formName)
    }

    const trackUserEngagement = (engagementType, content = '') => {
        gtag('event', 'user_engagement', {
            engagement_type: engagementType,
            content: content,
            page_title: document.title,
            page_location: window.location.href
        })
        console.log('👤 Engagement:', engagementType, content)
    }

    const trackDirectAccess = (contentType, contentTitle) => {
        const referrer = document.referrer
        const currentDomain = window.location.hostname

        // Detectar tipo de acceso
        if (!referrer) {
            // Sin referrer = acceso directo (URL escrita, marcador, QR, compartido)
            gtag('event', 'direct_access', {
                content_type: contentType,
                content_title: contentTitle,
                access_method: 'direct_url_or_qr',
                page_title: document.title,
                page_location: window.location.href
            })
            console.log('📱 Acceso directo detectado:', contentType, contentTitle)
            return 'direct'
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
                        page_location: window.location.href
                    })
                    console.log('🔗 Acceso desde dominio externo:', contentType, contentTitle, 'desde:', referrerDomain)
                    return 'external'
                }
            } catch (error) {
                // Error al parsear referrer, considerar como directo
                gtag('event', 'direct_access', {
                    content_type: contentType,
                    content_title: contentTitle,
                    access_method: 'unknown_referrer',
                    page_title: document.title,
                    page_location: window.location.href
                })
                console.log('📱 Acceso con referrer desconocido:', contentType, contentTitle)
                return 'direct'
            }
        }
        return 'internal'
    }

    const trackViewTime = (contentType, contentTitle, viewTimeSeconds) => {
        // Categorizar el tiempo de visualización
        let timeCategory = 'very_short' // < 5 segundos
        if (viewTimeSeconds >= 5 && viewTimeSeconds < 15) timeCategory = 'short'
        else if (viewTimeSeconds >= 15 && viewTimeSeconds < 30) timeCategory = 'medium'
        else if (viewTimeSeconds >= 30 && viewTimeSeconds < 60) timeCategory = 'long'
        else if (viewTimeSeconds >= 60) timeCategory = 'very_long'

        gtag('event', 'view_time', {
            content_type: contentType,
            content_title: contentTitle,
            view_time_seconds: viewTimeSeconds,
            time_category: timeCategory,
            page_title: document.title,
            page_location: window.location.href
        })
        console.log('⏱️ Tiempo de visualización:', contentType, viewTimeSeconds + 's', `(${timeCategory})`)
    }

    return {
        loadGoogleAnalytics,
        trackPageView,
        trackDownload,
        trackSearch,
        trackVideoPlay,
        trackAudioPlay,
        trackNewsletterSignup,
        trackContactForm,
        trackUserEngagement,
        trackDirectAccess,
        trackViewTime,
    }    // Función para configurar el consentimiento de cookies (GDPR)
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
        trackPageView,
        trackEvent,
        trackDownload,
        trackSearch,
        trackVideoPlay,
        trackAudioPlay,
        trackNewsletterSignup,
        trackContactForm,
        trackUserEngagement,
        grantConsent,
        denyConsent,
        measurementId
    };
}
