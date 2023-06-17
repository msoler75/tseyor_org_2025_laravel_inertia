<script setup>
import { ref } from 'vue';
import { Collapse } from 'vue-collapsed'
import { Icon } from '@iconify/vue';
import { vOnClickOutside } from '@vueuse/components'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    items: {
        type: Array,
        default: []
    }
});

const emit = defineEmits(['close']);

const close = () => {
    // console.log('close!')
        emit('close');
};



const isSidebarCollapsed = ref(false);


const toggleSubMenu = (event, menuItem) => {
    event.preventDefault();
    menuItem.open = !menuItem.open;
};

const toggleInnerSubMenu = (event, subMenuItem) => {
    event.preventDefault();
    subMenuItem.open = !subMenuItem.open;
};

const toggleSidebarCollapse = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    closePoppers();
};

const toggleSidebarToggled = () => {
    show.value = !show.value;
};

</script>

<template>
    <!-- Menú de barra lateral (sidebar) -->
    <div :class="{ '-translate-x-full': !show }"
        class="fixed z-40 inset-y-0 min-h-screen left-0 w-64 bg-gray-900 text-white transition-transform duration-300 ease-in-out transform"
        v-on-click-outside="close"
        >
        <div class="brand">Logo</div>
        <nav class="menu">




            <div v-for="menuItem in items" :key="menuItem.id" class="flex flex-col justify-start items-center   px-6 border-b border-gray-600 w-full  ">
    <button  @click="toggleSubMenu($event, menuItem)" class="focus:outline-none focus:text-indigo-400 text-left  text-white flex justify-between items-center w-full py-5 space-x-14  ">

        <a v-if="menuItem.subMenu" @click="toggleSubMenu($event, menuItem)">
                        <Icon icon="mdi:chevron-down" />
                        {{ menuItem.title }}
                    </a>
                    <a v-else>
                        <Icon :icon="menuItem.icon" />
                        {{ menuItem.title }}
                    </a>

      <svg  v-if="menuItem.subMenu" class="transform rotate-180" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M18 15L12 9L6 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
    </button>
    <div  class="flex justify-start flex-col w-full md:w-auto items-start pb-1">
      <button  v-if="menuItem.subMenu"  v-for="subMenuItem in menuItem.subItems" class="flex justify-start items-center space-x-6 hover:text-white focus:bg-gray-700 focus:text-white hover:bg-gray-700 text-gray-400 rounded px-3 py-2  w-full md:w-52" :key="subMenuItem.id">
        <svg class="fill-stroke" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M15 10L11 14L17 20L21 4L3 11L7 13L9 19L12 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
        <p class="text-base leading-4  ">Messages</p>
      </button>

    </div>
  </div>






            <ul>
                <li v-for="menuItem in items" :key="menuItem.id"
                    :class="{ 'menu-item': true, 'sub-menu': menuItem.subMenu }">
                    <a v-if="menuItem.subMenu" @click="toggleSubMenu($event, menuItem)">
                        <Icon icon="mdi:chevron-down" />
                        {{ menuItem.title }}
                    </a>
                    <a v-else>
                        <Icon :icon="menuItem.icon" />
                        {{ menuItem.title }}
                    </a>
                    <Collapse v-if="menuItem.subMenu" :when="menuItem.open" class="menu-item">
                        <ul>
                            <li v-for="subMenuItem in menuItem.subitems" :key="subMenuItem.id" class="menu-item sub-menu">
                                <a @click="toggleInnerSubMenu($event, subMenuItem)">
                                    <Icon icon="ion:chevron-up-circle-sharp" />
                                    {{ subMenuItem.title }}
                                </a>
                                <ul>
                                    <li v-for="innerSubMenuItem in subMenuItem.innerSubitems" :key="innerSubMenuItem.id"
                                        class="menu-item sub-menu">
                                        <a>
                                            <Icon :icon="innerSubMenuItem.icon" />
                                            {{ innerSubMenuItem.title }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </Collapse>
                </li>
            </ul>
        </nav>
    </div>
</template>



<style scoped>
/* Estilos Tailwind CSS */
.sidebar {
    /* ... */
}

.menu-item {
    transition: height 250ms cubic-bezier(0.3, 0, 0.6, 1);
}
</style>
