
export default function setTransitionPages(router) {
  const ui = useUi();
  const nav = ui.nav;

  const log = ()=>{}
  //console.log;

  // Evitar registrar los mismos listeners más de una vez
  if (typeof window !== "undefined") {
    if (window.__transitionPages_setTransitionPagesCalled) {
      log("router: setTransitionPages: already registered, skipping");
      return;
    }
    window.__transitionPages_setTransitionPagesCalled = true;
  }

  log("router: setTransitionPages: registering router listeners");

  // listen link-clicked events from Link.vue:
  window.addEventListener("link-clicked", (event) => {

      log('link-clicked', event)
      nextTick(() => startingNavigate(event))
    }
  );

  function scrollCondition(destinationUrl) {
    log("scrollCondition for", destinationUrl);
    if(!nav.navigatingFrom) return null
    const elem = nav.scrollToHereElem();
    if (!elem) return null;

    let url = destinationUrl;
    if (!url.match(/^http/)) url = window.location.origin + url;
    const nuevaRuta = new URL(url);
    const rutaOrigen = nav.navigatingFrom;

    const p1 = rutaOrigen.pathname.split("/");
    const p2 = nuevaRuta.pathname.split("/");

    const mismaRuta = rutaOrigen.origin == nuevaRuta.origin && p1[1] == p2[1];
    const mismaPagina =
      rutaOrigen.origin + rutaOrigen.pathname ==
      nuevaRuta.origin + nuevaRuta.pathname;

    if (
      (elem.classList.contains("if-same-page") && mismaPagina) ||
      (elem.classList.contains("if-same-path") && mismaRuta)
    ) {

      const behavior= elem.classList.contains("instant") ? "instant" : "smooth";
      log("go to scroll-to-here with behavior:", behavior);
      return behavior
    }

    return null;
  }

  function startingNavigate(event) {
    log("router: startingNavigate", event.detail.url, event);

    try {
        window.navigatingTo = new URL(event.detail.url).pathname + new URL(event.detail.url).search + new URL(event.detail.url).hash;
    }
    catch(e) {
        window.navigatingTo = event.detail.url;
    }

    ui.tools.closeTools();
    nav.closeTabs();

    nav.navigating = true;
    nav.navigatingFrom = new URL(window.location);

    log("router: start. Navigating from", nav.navigatingFrom);
    let url = event.detail.url;

    log("router: scroll-to-here url:", url, event.detail);

    const behavior = scrollCondition(url);
    if (behavior == "instant")
      setTimeout(() => nav.doScrollToHere(behavior), 200);
    else if (behavior == "smooth") nav.doScrollToHere(behavior);
    log("startingNavigate - preservePage:", nav.preservePage);
    if (!nav.preservePage) nav.fadingOutPage = true;
  }

  function endedNavigate(event) {
    log("router: endedNavigate", event);
    if (window.lastEndedNavigateUrl === event.detail.page.url && Date.now() - (window.lastEndedNavigateTime || 0) < 500) {
      log("router: endedNavigate skipped, duplicate within 500ms");
      return;
    }
    window.lastEndedNavigateUrl = event.detail.page.url;
    window.lastEndedNavigateTime = Date.now();

    ui.tools.detectContent();
    ui.tools.closeTools();

    nav.navigating = false;
    nav.fadingOutPage = false;
    nav.preservePage = false;

    const behavior = scrollCondition(event.detail.page.url);
    // enlace inicial
    if (window.location.hash) {
      setTimeout(() => {
        log("scrollto_3_hash");
        nav.scrollToId(window.location.hash.substring(1), 0);
      }, 500);
    } else if (behavior) {
      nav.doScrollToHere(behavior)
    } else {
      log("tr.scrollto_0_instant");
      window.scrollTo({
        top: 0,
        behavior: "instant",
      });
    }

    nav.navigating = false;
  }

  // Helper functions
  function isPartialReload(event) {
    const v = event?.detail?.visit || {};
    return v.only && Array.isArray(v.only) && v.only.length > 0;
  }

  function isPrefetch(event) {
    return event?.detail?.visit?.headers?.Purpose === "prefetch";
  }


  // Use the router's navigation guard to track route changes
  router.on("before", (event) => {
    log("router: before", event);
    nav.navigatingFrom = new URL(window.location);
  });

  router.on("success", (event) => {
    log("router: success", event);

    if (isPartialReload(event)) {
      log(
        "router: success ignored, partial reload with only:",
        event.detail.visit.only
      );
      return;
    }

  log('no es partial Reload')

  log('success navigatingTo', window.navigatingTo, 'event.detail.page.url', event.detail.page.url)
    if(event?.detail?.page?.url ===window.navigatingTo)
        endedNavigate(event);


  });

  // Use the router's navigation guard to track route changes
  router.on("start", (event) => {
    log("router: start", event);
    try {
      if (isPartialReload(event)) {
        log(
          "router: start ignored, partial reload with only:",
          event.detail.visit.only
        );
        return;
      }
      log("router: start checking if prefetch", event.detail);
      if (isPrefetch(event)) {
        log("router: start ignored, prefetch");
        return;
      }
      const v = event.detail.visit || {};
      log(
        `router: start. Starting a visit to ${v.url} (id: ${
          v.id || "<no-id>"
        }, method: ${v.method || "GET"}) isTrusted: ${
          event.isTrusted
        }, timeStamp: ${event.timeStamp}`,
        { visit: v, event }
      );
    } catch (e) {
      log("router: start (error logging event)", event);
    }
  });

  router.on("navigate", (event) => {
    if (isPrefetch(event)) {
      log("router: navigate ignored, prefetch");
      return;
    }
    log(
      `router: navigate. Navigated to ${event.detail.page.url}`,
      event
    );

    try {
      log("router: navigate event.detail", event.detail);
      if (isPartialReload(event)) {
        log(
          "router: navigate ignored, partial reload with only:",
          event.detail.visit.only
        );
        return;
      }
      log("router: navigate checking if prefetch", event);
      if (isPrefetch(event)) {
        log("router: navigate ignored, prefetch");
        return;
      }
      const v = event.detail || {};
      log(
        `router: navigate. Page navigating to ${v.url} (id: ${
          v.id || "<no-id>"
        })`,
        {
          visit: v,
          event,
        }
      );
    } catch (e) {
      log("router: navigate", event);
    }

    endedNavigate(event);
  });

  router.on("exception", (event) => {
    log(
      `router: exception. An unexpected error occurred during an Inertia visit.`,
      event
    );
    // console.log(event.detail.error);
    // nav.navigating = false
  });


  router.on("invalid", (event) => {
    log(`router: invalid. An invalid Inertia response was received.`);
    log(event.detail.response);
    nav.navigating = false
  });


  router.on("error", (errors) => {
    console.error("router: error !!!! - ", errors);
    nav.navigating = false;
    // reset state
    // nav.dontFadeout = false;
  });
}
