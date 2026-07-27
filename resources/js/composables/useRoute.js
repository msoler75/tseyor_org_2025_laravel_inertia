// resources/js/composables/useRoute.js
import { route as routeFn } from 'ziggy';
import { Ziggy } from '../ziggy.js';

export default function useRoute() {
  return (name, params, absolute, config = Ziggy) => routeFn(name, params, absolute, config);
}
