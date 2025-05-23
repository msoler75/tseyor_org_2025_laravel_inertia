<template>
  <div>
    <h1>Configuración de Suscripción al Boletín</h1>
    <form @submit.prevent="submitConfig">
      <div>
        <label>
          <input type="radio" v-model="servicio" value="boletin:semanal" />
          Semanal
        </label>
      </div>
      <div>
        <label>
          <input type="radio" v-model="servicio" value="boletin:quincenal" />
          bisemanal
        </label>
      </div>
      <div>
        <label>
          <input type="radio" v-model="servicio" value="boletin:mensual" />
          Mensual
        </label>
      </div>
      <div>
        <label>
          <input type="radio" v-model="servicio" value="darse_baja" />
          Darse de baja
        </label>
      </div>
      <button type="submit">Guardar configuración</button>
    </form>
  </div>
</template>

<script setup>

const servicio = ref('');

function submitConfig() {
    router.post(route('boletin.configurar'), { servicio: servicio.value });
}
</script>
