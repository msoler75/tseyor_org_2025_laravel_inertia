import {useNav} from "../Stores/nav.js"

const nav = useNav()

// console.log({page})
const animationPageTransitionDuration = 0

// Use the router's navigation guard to track route changes
router.on('start', (event) => {
    console.log(`router: start. Starting a visit to ${event.detail.visit.url}`, event.detail.visit.url)

    /*if(nav.ignoreScroll) {
        scrollToCurrentPosition()
        return
    }*/

    if(event.detail.visit.url==route('logout'))
    nav.dontFadeout = true

    nav.navigating = true

    const nuevaRuta = event.detail.visit.url
    const rutaActual = new URL(window.location)
    let mismaSeccion = false

    const fadeoutWhenNavigateTo = /^\/(audios|comunicados|contactos|entradas|equipos|eventos|experiencias|informes|libros|meditaciones|normativas|noticias|publicaciones|usuarios)\/.+/
    if(fadeoutWhenNavigateTo.exec(nuevaRuta.pathname)) {
        console.log('auto fadeOut')
        nav.fadeoutPage()
    }

    // to-do: si tenemos dos rutas: rutaActual = /glosario/1 y nuevaRuta = /glosario/2 entonces mismaSeccion ha de cambiar a true. Lo mismo si son dos rutas /libros/1 y /libros/juan
    // por lo tanto hemos de splitear las rutas y ver si coinciden en la primera palabra
    const p1 = rutaActual.pathname.split('/')
    const p2 = nuevaRuta.pathname.split('/')
    mismaSeccion = rutaActual.origin == nuevaRuta.origin && p1[1] == p2[1]
    // si, quitando la parte de query, son la misma ruta...
    console.log('comparing', nuevaRuta.origin + nuevaRuta.pathname, 'vs', rutaActual.origin + rutaActual.pathname)
    const mismapagina = nuevaRuta.origin + nuevaRuta.pathname == rutaActual.origin + rutaActual.pathname
    let scrolling = mismapagina || mismaSeccion
    if(nav.fadingOutPage) {

    }
    else if (nav.dontScroll) {
        if (!nav.dontFadeout)
            nav.fadeoutPage()
    } else {
        if (scrolling)
            nav.scrollToContent()
        else if (!nav.dontFadeout)
            nav.fadeoutPage()
        else
            nav.scrollToContent()
    }

    nav.dontScroll=false


})

router.on('navigate', (event) => {
    console.log(`router: navigate. Navigated to ${event.detail.page.url}`)
    nav.navigating = false
    // reset state
    nav.dontFadeout = false
    // nav.ignoreScroll = false

    if (nav.fadingOutPage) {
        window.scrollTo({
            top: 0,
            behavior: 'instant'
        });
        setTimeout(() => {
            nav.fadingOutPage = false
        }, animationPageTransitionDuration)

    }
})

router.on('exception', (event) => {
    console.log(`router: exception. An unexpected error occurred during an Inertia visit.`)
    console.log(event.detail.error)
})

router.on('invalid', (event) => {
    console.log(`router: invalid. An invalid Inertia response was received.`)
    console.log(event.detail.response)
})

router.on('error', (errors) => {
    console.log(errors)
})

router.on('success', (event) => {
    console.log(`router: success. Successfully made a visit to ${event.detail.page.url}`)
})

router.on('finish', (event) => {
    console.log(`router: finish. Page loaded ${event.detail.visit.url}`)
})
