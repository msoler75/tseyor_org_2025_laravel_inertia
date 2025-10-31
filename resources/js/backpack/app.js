import "../bootstrap";
// import "../../css/admin.css";

import { ZiggyVue } from "ziggy";
import { Ziggy } from "../ziggy.js";

// import { createApp } from "vue";
import { createApp, defineAsyncComponent } from "vue/dist/vue.esm-bundler.js";


// 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.3/vue.global.min.js'
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";

import TipTapEditorFullField from "../Components/Backpack/TipTapEditorFullField.vue";
import TipTapEditorSimpleField from "../Components/Backpack/TipTapEditorSimpleField.vue";
import ImageCoverField from "../Components/Backpack/ImageCoverField.vue";
// import JSONEditorField from "../Components/Backpack/JSONEditorField.vue";
import SelectField from "../Components/Backpack/SelectField.vue";
import TimeAgo from "../Components/TimeAgo.vue";
import FileManager from "../Components/FileManager.vue";
import WorkerStatus from "../Components/Admin/WorkerStatus.vue";
import { LazyHydrationWrapper } from 'vue3-lazy-hydration';

// Hacer defineAsyncComponent disponible globalmente para librerías externas
// window.defineAsyncComponent = defineAsyncComponent;

// only in forms
const elem = document.querySelector(".page-body form, .admin-dashboard, .vue-component");
if (elem) {
  console.log("loading vue 3 fields...");
  const app = createApp({})
  .component("tiptapeditorfullfield", TipTapEditorFullField)
  .component("tiptapeditorsimplefield", TipTapEditorSimpleField)
    //.component("quilleditorfullfield", QuillEditorFullField)
    //.component("quilleditorsimplefield", QuillEditorSimpleField)
    //.component("tinymcefullfield", TinyMCEFullField)
    //.component("tinymcesimplefield", TinyMCESimpleField)
    .component("imagecoverfield", ImageCoverField)
   // .component("jsoneditorfield", JSONEditorField)
    .component("selectfield", SelectField)
    .component("timeago", TimeAgo)
    .component("filemanager", FileManager)
    .component("workerstatus", WorkerStatus)
    // .component("audiovideoplayer", AudioVideoPlayer)
   // .component("audiostateicon", AudioStateIcon)
    .mixin({
      components: { Icon, Link },
    })
    .use(ZiggyVue, Ziggy)

      app.component('LazyHydrate', LazyHydrationWrapper);

    window.app = app.mount(elem)
}
