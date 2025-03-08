export function registerAsyncComponents(app: any) {
  const asyncComponentsGlob = import.meta.glob('./resources/js/AsyncComponents/**/*.vue');

  Object.entries(asyncComponentsGlob).forEach(([path, importFn]) => {
    const componentName = path.split('/').pop().replace('.vue', '').replace(/-(w)/g, (_, c) => c.toUpperCase());
    app.component(componentName, defineAsyncComponent(() => importFn()));
  });

  console.log('Componentes asíncronos registrados:', Object.keys(asyncComponentsGlob).length);
}
