import "../bootstrap";

// https://github.com/John-Weeks-Dev/facebook-clone/blob/master/resources/js/app.js
//import { createPinia } from "pinia";

import { ZiggyVue } from "ziggy";
import { Ziggy } from "../ziggy.js";

// resources/js/app.js
// import { createApp } from "vue";
import { createApp } from "vue/dist/vue.esm-bundler.js";

// 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.3.3/vue.global.min.js'
import { Icon } from "@iconify/vue";
import { Link } from "@inertiajs/vue3";
import FloatingVue from 'floating-vue'

// my components
import QuillEditorField from "../Components/QuillEditorField.vue";
/* import FolderExplorer from "../Components/FolderExplorer.vue";
import FolderIcon from "../Components/FolderIcon.vue";
import FileIcon from "../Components/FileIcon.vue";
import Spinner from "../Components/Spinner.vue";
import Card from "../Components/Card.vue";
import Dropdown from "../Components/Dropdown.vue";
import FileSize from "../Components/FileSize.vue";
import TimeAgo from "../Components/TimeAgo.vue";
import Modal from "../Components/Modal.vue";
import ConfirmationModal from "../Components/ConfirmationModal.vue";
import Image from "../Components/Image.vue";
import ConditionalLink from "../Components/ConditionalLink.vue";
import Breadcrumb from "../Components/Breadcrumb.vue";
*/

//const pinia = createPinia();
console.log("app initiating...");
window.app = createApp({})
/*
app.component("FolderExplorer", FolderExplorer);
app.component("FileManager", FileManager);
app.component("FolderIcon", FolderIcon);
app.component("Icon", Icon);
app.component("FileIcon", FileIcon);
app.component("Spinner", Spinner);
app.component("Card", Card);
app.component("Link", Link);
app.component("ConditionalLink", ConditionalLink);
app.component("Breadcrumb", Breadcrumb);
app.component("Dropdown", Dropdown);
app.component("FileSize", FileSize);
app.component("TimeAgo", TimeAgo);
app.component("Modal", Modal);
app.component("ConfirmationModal", ConfirmationModal);
app.component("Image", Image);
*/
.component("quilleditorfield", QuillEditorField)
.use(FloatingVue)
.mixin({
  components: {Icon,Link },
})
.use(ZiggyVue, Ziggy)
//.use(pinia);



