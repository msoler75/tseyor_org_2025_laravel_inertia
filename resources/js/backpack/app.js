import "../bootstrap";

import { ZiggyVue } from "ziggy";
import { Ziggy } from "../ziggy.js";

// import { createApp } from "vue";
import { createApp } from "vue/dist/vue.esm-bundler.js";

// 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.3/vue.global.min.js'
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";

import TinyMCEFullField from "../Components/TinyMCEFullField.vue";
import TinyMCESimpleField from "../Components/TinyMCESimpleField.vue";
import QuillEditorField from "../Components/QuillEditorField.vue";
import ImageCoverField from "../Components/ImageCoverField.vue";

console.log("app initiating...");
window.app = createApp({})
  .component("quilleditorfield", QuillEditorField)
  .component("tinymcefullfield", TinyMCEFullField)
  .component("tinymcesimplefield", TinyMCESimpleField)
  .component("imagecoverfield", ImageCoverField)
  .mixin({
    components: { Icon, Link },
  })
  .use(ZiggyVue, Ziggy)
  .mount(".page");
