<template>
  <div class="fechas-evento-vue">
    <table class="max-w-fit mb-2" style="border-spacing: 2px;">
      <tbody>
        <tr v-for="(d, idx) in dates" :key="idx">
          <td style="max-width: 160px">
            <input type="date" :name="fieldName + '[]'" class="form-control max-w-[140px]" :value="dates[idx]" @input="(e) => onInput(e, idx)">
          </td>
          <td class="pl-2">
            <button v-if="!removeDisabled(idx)" type="button" class="btn btn-primary btn-sm remove-fecha w-10" @click="remove(idx)">-</button>
          </td>
        </tr>
      </tbody>
    </table>
    <button type="button" class="btn btn-secondary btn-xs" style="margin-left: 66px" @click="add">Añadir Fecha</button><br>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const { fieldName, initialValue } = defineProps({
  fieldName: { type: String, required: true },
  initialValue: { type: [Array, String], default: () => [] },
});

function formatYmd(val) {
  if (!val && val !== 0) return null;
  const s = String(val).trim();
  if (!s) return null;
  if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
  const d = new Date(s);
  if (isNaN(d.getTime())) return null;
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

function parseInitial(val) {
  if (!val && val !== 0) return [''];
  if (Array.isArray(val)) {
    const arr = val.map(v => formatYmd(v)).filter(x => x !== null);
    return arr.length ? arr : [''];
  }
  if (typeof val === 'string') {
    try {
      const parsed = JSON.parse(val);
      if (Array.isArray(parsed)) {
        const arr = parsed.map(v => formatYmd(v)).filter(x => x !== null);
        return arr.length ? arr : [''];
      }
    } catch (e) {}
    const parts = val.split(',').map(s => s.trim()).map(s => formatYmd(s)).filter(x => x !== null);
    return parts.length ? parts : [''];
  }
  return [''];
}

const dates = ref(parseInitial(initialValue));

function add() { dates.value.push(''); }

function set(idx, val) {
  const v = formatYmd(val) || '';
  dates.value[idx] = v;
}

function onInput(e, idx) { set(idx, e.target.value); }

function remove(idx) {
  if (dates.value.length <= 1) {
    dates.value[0] = '';
  } else {
    dates.value.splice(idx, 1);
  }
}

function removeDisabled(idx) {
  if (dates.value.length <= 1) return !(dates.value[0] && dates.value[0].trim() !== '');
  return false;
}
</script>

<style scoped>
.fechas-evento-vue .remove-fecha[disabled] { opacity: 0.6; cursor: not-allowed; }
</style>
