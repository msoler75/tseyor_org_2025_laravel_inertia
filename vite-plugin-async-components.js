import fs from 'fs';



export default function asyncComponentsPlugin() {
  return {
    name: 'vite-plugin-async-components',
    configResolved(config) {
        const outputPath = `${config.root}async_components.d.ts`;
        let content = ''



      content += `export function registerAsyncComponents(app: any) {\n`;
      content += `  const asyncComponentsGlob = import.meta.glob('./resources/js/AsyncComponents/**/*.vue');\n\n`;
      content += `  Object.entries(asyncComponentsGlob).forEach(([path, importFn]) => {\n`;
      content += `    const componentName = path.split('/').pop().replace('.vue', '').replace(/-(\w)/g, (_, c) => c.toUpperCase());\n`;
      content += `    app.component(componentName, defineAsyncComponent(() => importFn()));\n`;
      content += `  });\n\n`;
      content += `  console.log('Componentes asíncronos registrados:', Object.keys(asyncComponentsGlob).length);\n`;
      content += `}\n`;

      fs.writeFileSync(outputPath, content);
    }
  };
}
