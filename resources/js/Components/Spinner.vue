<template>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="0.99em" height="1em" viewBox="0 0 24 24">
  <component is="style">
    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @keyframes dash {
      0% {
        stroke-dasharray: 0 150;
        stroke-dashoffset: 0;
      }
      50% {
        stroke-dasharray: 42 150;
        stroke-dashoffset: -16;
      }
      100% {
        stroke-dasharray: 42 150;
        stroke-dashoffset: -59;
      }
    }

    .spinner {
      animation: rotate 2s linear infinite, dash 1.5s ease-in-out infinite;
      transform-origin: center;
    }
  </component>
  <g stroke="currentColor">
    <circle cx="12" cy="12" r="9.5" fill="none" stroke-linecap="round" stroke-width="3" class="spinner"></circle>
  </g>
</svg>
</template>
