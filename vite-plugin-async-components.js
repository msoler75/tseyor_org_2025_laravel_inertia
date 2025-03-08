import fs from 'fs';
import path from 'path';
import glob from "glob";



export default function asyncComponentsPlugin() {
  return {
    name: 'vite-plugin-async-components',
    configResolved(config) {
        const outputPath = path.resolve(config.root, 'async_components.d.ts');
        let content = ''
        if(false) {

            const asyncComponentsPath = path.resolve(config.root, 'resources/js/AsyncComponents');

      const vueFiles = glob.sync('**/*.vue', { cwd: asyncComponentsPath });

      let content = `export {}\n\ndeclare module 'vue' {\n  export interface GlobalComponents {\n`;

      vueFiles.forEach(file => {
        const componentName = path.basename(file, '.vue');
        content += `    ${componentName}: typeof import('./resources/js/AsyncComponents/${file}')['default']\n`;
    });
    content += '  }\n}\n\n';
    }


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
