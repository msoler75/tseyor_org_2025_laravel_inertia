import "../bootstrap";

import { ZiggyVue } from "ziggy";
import { Ziggy } from "../ziggy.js";

// import { createApp } from "vue";
import { createApp } from "vue/dist/vue.esm-bundler.js";

// 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.3/vue.global.min.js'
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";

import EditorFullField from "../Components/EditorFullField.vue";
import EditorSimpleField from "../Components/EditorSimpleField.vue";
import ImageCoverField from "../Components/ImageCoverField.vue";

console.log("app initiating...");
window.app = createApp({})
  .component("editorfullfield", EditorFullField)
  .component("editorsimplefield", EditorSimpleField)
  .component("imagecoverfield", ImageCoverField)
  .mixin({
    components: { Icon, Link },
  })
  .use(ZiggyVue, Ziggy)
  .mount(".page")
  ;
