// resources/js/composables/useRoute.js
import Router from '../../../vendor/tightenco/ziggy/src/js/Router.js';
import { Ziggy } from '../ziggy.js';

export default function useRoute() {
  const route = (name, params, absolute, config = Ziggy) => {
    const router = new Router(name, params, absolute, config);
    return name ? router.toString() : router;
  };

  return route; 
}
