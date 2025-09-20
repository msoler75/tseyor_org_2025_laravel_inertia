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

    const trackSearch = (searchTerm) => {
        trackEvent('search', {
            search_term: searchTerm,
        });
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
        trackEvent('generate_lead', {
            form_name: formName,
        });
    };

    const trackUserEngagement = (engagementType, content = '') => {
        trackEvent('engagement', {
            engagement_type: engagementType,
            content: content,
        });
    };

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
