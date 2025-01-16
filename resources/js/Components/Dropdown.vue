<template>
    <div ref="target" :class="relative ? 'relative' : ''">
      <div @click="handleToggle">
        <slot name="trigger" :open="open"/>
      </div>

      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-75"
        leave-from-class="transform opacity-100 scale-100"
        leave-to-class="transform opacity-0 scale-95"
      >
        <div
          v-show="open"
          class="absolute z-30 mt-2 rounded-md shadow-lg"
          :class="[widthClass, alignmentClasses]"
          style="display: none; transform-origin: top right;"
          @click="open = false"
          ref="dropdownMenu"
        >
          <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
            <slot name="content" />
          </div>
        </div>
      </transition>
    </div>
  </template>

  <script setup>
  import { onClickOutside } from '@vueuse/core'

  const props = defineProps({
    align: {
      type: String,
      default: 'right',
    },
    width: {
      type: String,
      default: '48',
    },
    contentClasses: {
      type: Array,
      default: () => ['py-1', 'bg-slate-100 dark:bg-slate-700'],
    },
    // propiedad para definir si el wrapper es relative o no:
    relative: {
      type: Boolean,
      default: true,
    },
  });

  let open = ref(false);

  const target=ref(null)

  onClickOutside(target, event => open.value = false)

  const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
      open.value = false;
    }
  };

  onMounted(() => document.addEventListener('keydown', closeOnEscape));
  onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

  const widthClass = computed(() => {
    if(props.width=='auto')
      return 'w-auto max-w-screen'
    return {
      '48': 'w-48',
      '60': 'w-60',
    }[props.width.toString()];
  });

  const alignmentClasses = computed(() => {
    if (props.align === 'left') {
      return 'origin-top-left left-0';
    }

    if (props.align === 'right') {
      return 'origin-top-right right-0';
    }

    return 'origin-top';
  });

  const dropdownMenu = ref(null)

  const setPosition = () => {
  const dropdown = dropdownMenu.value;

  if (!dropdown) return;

   // Restablecer transformaciones
   dropdown.style.transform = 'none';

  // Hacer visible el dropdown con opacidad 0
  dropdown.style.display = 'block';
  dropdown.style.opacity = '0';

  // Esperar al próximo frame antes de obtener el rect
  requestAnimationFrame(() => {
    const rect = dropdown.getBoundingClientRect();
    const windowWidth = window.innerWidth;
    const windowHeight = window.innerHeight;

    let transformValue = '';

    if (rect.right > windowWidth) {
      transformValue += `translateX(-${rect.right - windowWidth}px) `;
    }

    if (rect.bottom > windowHeight) {
      transformValue += `translateY(-${rect.bottom - windowHeight}px) `;
    }

    // Cambiar la opacidad a 1
    dropdown.style.opacity = '1';

    // Aplicar la transformación
    dropdown.style.transform = transformValue.trim();
  });
};


  const handleToggle = () => {
    open.value = !open.value;
    if (open.value) {
      setPosition();
    }
  };
  </script>

  <style scoped>
  /* Add your custom styles here */
  </style>
