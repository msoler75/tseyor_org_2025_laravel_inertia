const Ziggy = {
  url: "http://localhost:8000",
  port: 8000,
  defaults: {},
  routes: {
    "backpack.auth.login": { uri: "admin/login", methods: ["GET", "HEAD"] },
    "backpack.auth.logout": { uri: "admin/logout", methods: ["GET", "HEAD"] },
    "backpack.auth.register": {
      uri: "admin/register",
      methods: ["GET", "HEAD"],
    },
    "backpack.auth.password.reset": {
      uri: "admin/password/reset",
      methods: ["GET", "HEAD"],
    },
    "backpack.auth.password.reset.token": {
      uri: "admin/password/reset/{token}",
      methods: ["GET", "HEAD"],
    },
    "backpack.auth.password.email": {
      uri: "admin/password/email",
      methods: ["POST"],
    },
    "backpack.dashboard": { uri: "admin/dashboard", methods: ["GET", "HEAD"] },
    backpack: { uri: "admin", methods: ["GET", "HEAD"] },
    "backpack.account.info": {
      uri: "admin/edit-account-info",
      methods: ["GET", "HEAD"],
    },
    "backpack.account.info.store": {
      uri: "admin/edit-account-info",
      methods: ["POST"],
    },
    "backpack.account.password": {
      uri: "admin/change-password",
      methods: ["POST"],
    },
    "user.index": { uri: "admin/user", methods: ["GET", "HEAD"] },
    "user.search": { uri: "admin/user/search", methods: ["POST"] },
    "user.showDetailsRow": {
      uri: "admin/user/{id}/details",
      methods: ["GET", "HEAD"],
    },
    "user.create": { uri: "admin/user/create", methods: ["GET", "HEAD"] },
    "user.store": { uri: "admin/user", methods: ["POST"] },
    "user.edit": { uri: "admin/user/{id}/edit", methods: ["GET", "HEAD"] },
    "user.update": { uri: "admin/user/{id}", methods: ["PUT"] },
    "user.destroy": { uri: "admin/user/{id}", methods: ["DELETE"] },
    "user.show": { uri: "admin/user/{id}/show", methods: ["GET", "HEAD"] },
    "comunicado.index": { uri: "admin/comunicado", methods: ["GET", "HEAD"] },
    "comunicado.search": { uri: "admin/comunicado/search", methods: ["POST"] },
    "comunicado.showDetailsRow": {
      uri: "admin/comunicado/{id}/details",
      methods: ["GET", "HEAD"],
    },
    "comunicado.create": {
      uri: "admin/comunicado/create",
      methods: ["GET", "HEAD"],
    },
    "comunicado.store": { uri: "admin/comunicado", methods: ["POST"] },
    "comunicado.edit": {
      uri: "admin/comunicado/{id}/edit",
      methods: ["GET", "HEAD"],
    },
    "comunicado.update": { uri: "admin/comunicado/{id}", methods: ["PUT"] },
    "comunicado.destroy": { uri: "admin/comunicado/{id}", methods: ["DELETE"] },
    "comunicado.show": {
      uri: "admin/comunicado/{id}/show",
      methods: ["GET", "HEAD"],
    },
    "debugbar.openhandler": { uri: "_debugbar/open", methods: ["GET", "HEAD"] },
    "debugbar.clockwork": {
      uri: "_debugbar/clockwork/{id}",
      methods: ["GET", "HEAD"],
    },
    "debugbar.assets.css": {
      uri: "_debugbar/assets/stylesheets",
      methods: ["GET", "HEAD"],
    },
    "debugbar.assets.js": {
      uri: "_debugbar/assets/javascript",
      methods: ["GET", "HEAD"],
    },
    "debugbar.cache.delete": {
      uri: "_debugbar/cache/{key}/{tags?}",
      methods: ["DELETE"],
    },
    login: { uri: "login", methods: ["GET", "HEAD"] },
    logout: { uri: "logout", methods: ["POST"] },
    "password.request": { uri: "forgot-password", methods: ["GET", "HEAD"] },
    "password.reset": {
      uri: "reset-password/{token}",
      methods: ["GET", "HEAD"],
    },
    "password.email": { uri: "forgot-password", methods: ["POST"] },
    "password.update": { uri: "reset-password", methods: ["POST"] },
    register: { uri: "register", methods: ["GET", "HEAD"] },
    "verification.notice": { uri: "email/verify", methods: ["GET", "HEAD"] },
    "verification.verify": {
      uri: "email/verify/{id}/{hash}",
      methods: ["GET", "HEAD"],
    },
    "verification.send": {
      uri: "email/verification-notification",
      methods: ["POST"],
    },
    "user-profile-information.update": {
      uri: "user/profile-information",
      methods: ["PUT"],
    },
    "user-password.update": { uri: "user/password", methods: ["PUT"] },
    "password.confirmation": {
      uri: "user/confirmed-password-status",
      methods: ["GET", "HEAD"],
    },
    "password.confirm": { uri: "user/confirm-password", methods: ["POST"] },
    "profile.show": { uri: "user/profile", methods: ["GET", "HEAD"] },
    "other-browser-sessions.destroy": {
      uri: "user/other-browser-sessions",
      methods: ["DELETE"],
    },
    "current-user-photo.destroy": {
      uri: "user/profile-photo",
      methods: ["DELETE"],
    },
    "current-user.destroy": { uri: "user", methods: ["DELETE"] },
    "sanctum.csrf-cookie": {
      uri: "sanctum/csrf-cookie",
      methods: ["GET", "HEAD"],
    },
    "ignition.healthCheck": {
      uri: "_ignition/health-check",
      methods: ["GET", "HEAD"],
    },
    "ignition.executeSolution": {
      uri: "_ignition/execute-solution",
      methods: ["POST"],
    },
    "ignition.updateConfig": {
      uri: "_ignition/update-config",
      methods: ["POST"],
    },
    comentarios: { uri: "api/comentarios", methods: ["GET", "HEAD"] },
    "comentario.nuevo": { uri: "api/comentarios", methods: ["POST"] },
    "files.upload.file": { uri: "api/files/upload/file", methods: ["POST"] },
    "files.upload.image": { uri: "api/files/upload/image", methods: ["POST"] },
    "files.rename": { uri: "api/files/rename", methods: ["POST"] },
    "files.move": { uri: "api/files/move", methods: ["POST"] },
    "files.copy": { uri: "api/files/copy", methods: ["POST"] },
    "files.mkdir": { uri: "api/files/mkdir", methods: ["PUT"] },
    "files.delete": {
      uri: "api/files{ruta}",
      methods: ["DELETE"],
      wheres: { ruta: "(\\/.+)?" },
    },
    portada: { uri: "/", methods: ["GET", "HEAD"] },
    novedades: { uri: "novedades", methods: ["GET", "HEAD"] },
    archivos0: { uri: "archivos", methods: ["GET", "HEAD"] },
    archivos: {
      uri: "archivos{ruta}",
      methods: ["GET", "HEAD"],
      wheres: { ruta: "(\\/.+)?" },
    },
    filemanager: {
      uri: "filemanager{ruta}",
      methods: ["GET", "HEAD"],
      wheres: { ruta: "(\\/.*)?" },
    },
    storage: {
      uri: "storage/{ruta}",
      methods: ["GET", "HEAD"],
      wheres: { ruta: "(\\/.+)?" },
    },
    audios: { uri: "audios", methods: ["GET", "HEAD"] },
    audio: { uri: "audios/{id}", methods: ["GET", "HEAD"] },
    videos: { uri: "videos", methods: ["GET", "HEAD"] },
    noticias: { uri: "noticias", methods: ["GET", "HEAD"] },
    noticia: { uri: "noticias/{id}", methods: ["GET", "HEAD"] },
    comunicados: { uri: "comunicados", methods: ["GET", "HEAD"] },
    comunicado: { uri: "comunicados/{id}", methods: ["GET", "HEAD"] },
    "archivo.comunicados": {
      uri: "archivo/comunicados",
      methods: ["GET", "HEAD"],
    },
    libros: { uri: "libros", methods: ["GET", "HEAD"] },
    libro: { uri: "libros/{id}", methods: ["GET", "HEAD"] },
    entradas: { uri: "entradas", methods: ["GET", "HEAD"] },
    entrada: { uri: "entradas/{id}", methods: ["GET", "HEAD"] },
    "guias": {
      uri: "guias",
      methods: ["GET", "HEAD"],
    },
    "guia": {
      uri: "guias/{id}",
      methods: ["GET", "HEAD"],
    },
    "lugares": {
      uri: "lugares",
      methods: ["GET", "HEAD"],
    },
    "lugar": {
      uri: "lugares/{id}",
      methods: ["GET", "HEAD"],
    },
    eventos: { uri: "eventos", methods: ["GET", "HEAD"] },
    evento: { uri: "eventos/{id}", methods: ["GET", "HEAD"] },
    contactos: { uri: "donde-estamos", methods: ["GET", "HEAD"] },
    contacto: { uri: "contactos/{id}", methods: ["GET", "HEAD"] },
    centros: { uri: "centros", methods: ["GET", "HEAD"] },
    centro: { uri: "centros/{id}", methods: ["GET", "HEAD"] },
    "quienes-somos": { uri: "quienes-somos", methods: ["GET", "HEAD"] },
    "origenes-de-tseyor": {
      uri: "origenes-de-tseyor",
      methods: ["GET", "HEAD"],
    },
    filosofia: { uri: "filosofia", methods: ["GET", "HEAD"] },
    cursos: { uri: "cursos", methods: ["GET", "HEAD"] },
    radio: { uri: "radio", methods: ["GET", "HEAD"] },
    "cursos.inscripcion": {
      uri: "cursos/inscripcion",
      methods: ["GET", "HEAD"],
    },
    "inscripcion.store": { uri: "inscripcion", methods: ["POST"] },
    ong: { uri: "ong", methods: ["GET", "HEAD"] },
    muular: { uri: "ong/muular", methods: ["GET", "HEAD"] },
    utg: { uri: "utg", methods: ["GET", "HEAD"] },
    "utg.departamentos": { uri: "utg/departamentos", methods: ["GET", "HEAD"] },
    "utg.departamento": {
      uri: "utg/departamentos/{id}",
      methods: ["GET", "HEAD"],
    },
    usuarios: { uri: "usuarios", methods: ["GET", "HEAD"] },
    "usuarios.buscar": {
      uri: "usuarios/_buscar/{buscar}",
      methods: ["GET", "HEAD"],
    },
    usuario: { uri: "usuarios/{id}", methods: ["GET", "HEAD"] },
    login1: { uri: "login/1", methods: ["GET", "HEAD"] },
    login2: { uri: "login/2", methods: ["GET", "HEAD"] },
    dashboard: { uri: "dashboard", methods: ["GET", "HEAD"] },
    equipos: { uri: "equipos", methods: ["GET", "HEAD"] },
    "equipo.crear": { uri: "equipos/nuevo", methods: ["GET", "HEAD"] },
    "equipo.nuevo": { uri: "equipos", methods: ["POST"] },
    equipo: { uri: "equipos/{id}", methods: ["GET", "HEAD"] },
    invitar: { uri: "invitar/{idEquipo}", methods: ["POST"] },
    "invitacion.aceptar": {
      uri: "invitacion/{token}/aceptar",
      methods: ["GET", "HEAD"],
    },
    "invitacion.declinar": {
      uri: "invitacion/{token}/declinar",
      methods: ["GET", "HEAD"],
    },
    "equipo.solicitudes": {
      uri: "equipos/{id}/solicitudes",
      methods: ["GET", "HEAD"],
    },
    "equipo.solicitar": {
      uri: "equipos/{id}/solicitar",
      methods: ["GET", "HEAD"],
    },
    "solicitud.aceptar": {
      uri: "solicitud/{id}/aceptar",
      methods: ["GET", "HEAD"],
    },
    "solicitud.denegar": {
      uri: "solicitud/{id}/denegar",
      methods: ["GET", "HEAD"],
    },
    "equipo.agregar": {
      uri: "equipos/{idEquipo}/{idUsuario}/agregar",
      methods: ["PUT"],
    },
    "equipo.remover": {
      uri: "equipos/{idEquipo}/{idUsuario}/remover",
      methods: ["PUT"],
    },
    "equipo.modificar": { uri: "equipos/{id}", methods: ["POST"] },
    "equipo.modificarRol": {
      uri: "equipos/{idEquipo}/update/{idUsuario}/{rol}",
      methods: ["PUT"],
    },
  },
};

if (typeof window !== "undefined" && typeof window.Ziggy !== "undefined") {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
