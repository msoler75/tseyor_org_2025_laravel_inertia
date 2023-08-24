import "../bootstrap";

import { ZiggyVue } from "ziggy";
import { Ziggy } from "../ziggy.js";

// import { createApp } from "vue";
import { createApp } from "vue/dist/vue.esm-bundler.js";

// 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.3/vue.global.min.js'
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";

import TinyMCEFullField from "../Components/Backpack/TinyMCEFullField.vue";
import TinyMCESimpleField from "../Components/Backpack/TinyMCESimpleField.vue";
import QuillEditorFullField from "../Components/Backpack/QuillEditorFullField.vue";
import QuillEditorSimpleField from "../Components/Backpack/QuillEditorSimpleField.vue";
import ImageCoverField from "../Components/Backpack/ImageCoverField.vue";
import JSONEditorField from "../Components/Backpack/JSONEditorField.vue";
import SelectField from "../Components/Backpack/SelectField.vue";

// only in forms
const elem = document.querySelector(".page form[method='post']");
if (elem) {
  console.log("loading vue 3 fields...");
  window.app = createApp({})
    .component("quilleditorfullfield", QuillEditorFullField)
    .component("quilleditorsimplefield", QuillEditorSimpleField)
    .component("tinymcefullfield", TinyMCEFullField)
    .component("tinymcesimplefield", TinyMCESimpleField)
    .component("imagecoverfield", ImageCoverField)
    .component("jsoneditorfield", JSONEditorField)
    .component("selectfield", SelectField)
    .mixin({
      components: { Icon, Link },
    })
    .use(ZiggyVue, Ziggy)
    .mount(elem);
}
