// analyze-components.mjs
import { glob } from "glob";
import fs from "fs";
import path from "path";
import { fileURLToPath } from 'url';
import { dirname } from 'path';

// Obtener el directorio actual cuando se usa ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Conjunto para rastrear componentes ya visitados y evitar ciclos
const visitedComponents = new Set();
// Mapa para rastrear importaciones de componentes de librerías externas
const externalComponents = new Map();
// Conjunto para componentes recursivos conocidos
const recursiveComponents = new Set(['ContentNode']);

// Estructura para mantener información sobre los componentes
const componentsInfo = {};

async function analyzeComponent(filePath, depth = 0, ancestry = []) {
    const canonicalPath = path.resolve(filePath);
    const componentName = path.basename(filePath, path.extname(filePath));

    // Verificar si ya visitamos este componente en esta rama del árbol
    if (ancestry.includes(canonicalPath)) {
        console.log(`  CICLO DETECTADO: ${componentName} ya fue analizado en esta rama`);
        // Marcar la recursividad en el componente padre para futuras referencias
        const parentPath = ancestry[ancestry.length - 1];
        if (parentPath && componentsInfo[parentPath]) {
            if (!componentsInfo[parentPath].recursiveDeps) {
                componentsInfo[parentPath].recursiveDeps = new Set();
            }
            componentsInfo[parentPath].recursiveDeps.add(componentName);
        }
        return depth;
    }

    // Si es un componente conocido como recursivo, y ya lo hemos analizado en otro lugar
    if (recursiveComponents.has(componentName) && visitedComponents.has(canonicalPath)) {
        console.log(`  COMPONENTE RECURSIVO CONOCIDO: ${componentName}`);
        return depth;
    }

    console.log(`Analizando componente: ${filePath}, profundidad: ${depth}`);

    // Registrar este componente como visitado
    visitedComponents.add(canonicalPath);

    // Crear entrada en el registro de información si no existe
    if (!componentsInfo[canonicalPath]) {
        componentsInfo[canonicalPath] = {
            name: componentName,
            dependencies: new Set(),
            recursiveDeps: new Set(),
            depth: depth,
            maxChildDepth: 0
        };
    }

    if (!fs.existsSync(filePath)) {
        console.error(`El archivo no existe: ${filePath}`);
        return depth;
    }

    const content = fs.readFileSync(filePath, "utf-8");

    // Detectar si este componente se llama a sí mismo (es recursivo)
    if (content.includes(`<${componentName}`) || content.includes(`<${componentName} `)) {
        recursiveComponents.add(componentName);
        console.log(`  COMPONENTE RECURSIVO DETECTADO: ${componentName}`);
    }

    // Detectar importaciones de librerías para evitar ciclos
    const importRegex = /import\s+(?:{([^}]+)}|([^\s;]+))\s+from\s+['"]([^'"]+)['"]/g;
    let importMatch;
    while ((importMatch = importRegex.exec(content)) !== null) {
        const importNames = importMatch[1] || importMatch[2];
        const packageName = importMatch[3];

        // Si viene de un paquete externo (no es una ruta relativa)
        if (!packageName.startsWith('.') && !packageName.startsWith('/')) {
            importNames.split(',').forEach(name => {
                const cleanName = name.trim().split(' as ')[0];
                externalComponents.set(cleanName, packageName);
                console.log(`  Detectada importación externa: ${cleanName} de ${packageName}`);
            });
        }
    }

    // Mejor regex para detectar componentes Vue (busca etiquetas PascalCase)
    const componentRegex = /<([A-Z][a-zA-Z0-9]*)(\s[^>]*)?\/?>|<([A-Z][a-zA-Z0-9]*)(\s[^>]*)?>([\s\S]*?)<\/\3>/g;
    let match;
    let maxDepth = depth;
    let components = new Set();

    // Nueva ancestría para este componente
    const newAncestry = [...ancestry, canonicalPath];

    while ((match = componentRegex.exec(content)) !== null) {
        const childComponentName = match[1] || match[3]; // Obtener el nombre del componente

        if (!childComponentName ||
            childComponentName.startsWith('template') ||
            childComponentName.startsWith('script') ||
            childComponentName === 'slot' ||
            childComponentName === 'component') {
            continue; // Ignorar etiquetas que no son componentes
        }

        if (components.has(childComponentName)) {
            continue; // Evitar duplicados
        }

        components.add(childComponentName);

        // Agregar a la lista de dependencias
        componentsInfo[canonicalPath].dependencies.add(childComponentName);

        // Si el componente se llama a sí mismo, evitar la recursión infinita
        if (childComponentName === componentName) {
            recursiveComponents.add(componentName);
            console.log(`  AUTO-REFERENCIA: ${componentName} se llama a sí mismo`);
            continue;
        }

        // Verificar si es un componente de librería externa
        if (externalComponents.has(childComponentName)) {
            console.log(`  Componente externo: ${childComponentName} de ${externalComponents.get(childComponentName)}`);
            continue; // No seguir analizando componentes externos
        }

        // Detectar componentes recursivos conocidos para evitar procesarlos múltiples veces
        if (recursiveComponents.has(childComponentName) && childComponentName !== componentName) {
            console.log(`  Componente recursivo conocido: ${childComponentName}, limitando análisis`);
            // Aún así buscamos su archivo para tenerlo en cuenta, pero limitamos la recursión
            const childPath = await findComponentPath(childComponentName, path.dirname(filePath));
            if (childPath) {
                componentsInfo[canonicalPath].dependencies.add(childComponentName);
                maxDepth = Math.max(maxDepth, depth + 1);
            }
            continue;
        }

        console.log(`  Componente secundario encontrado: ${childComponentName}`);

        const childPath = await findComponentPath(childComponentName, path.dirname(filePath));
        if (childPath) {
            // Evitar ciclos comparando rutas absolutas normalizadas
            const absComponentPath = path.resolve(childPath);

            // Si ya lo analizamos en otro lugar con profundidad suficiente, reutilizamos
            if (visitedComponents.has(absComponentPath) && componentsInfo[absComponentPath]) {
                console.log(`  Reutilizando análisis previo para: ${childComponentName}`);
                const childInfo = componentsInfo[absComponentPath];
                maxDepth = Math.max(maxDepth, depth + 1 + childInfo.maxChildDepth);
                continue;
            }

            const childDepth = await analyzeComponent(childPath, depth + 1, newAncestry);
            maxDepth = Math.max(maxDepth, childDepth);
        } else {
            console.log(`  No se pudo encontrar la ruta para el componente: ${childComponentName}`);
        }
    }

    // Actualizar la profundidad máxima de hijos
    componentsInfo[canonicalPath].maxChildDepth = maxDepth - depth;

    console.log(`Componente analizado: ${filePath}, profundidad total: ${maxDepth}`);
    return maxDepth;
}

async function findComponentPath(componentName, currentDir) {
    // Ampliar búsqueda para incluir más directorios
    const rootDir = process.cwd();
    const possibleDirs = [
        currentDir,
        path.join(rootDir, 'resources/js/Components'),
        path.join(rootDir, 'resources/js/Layouts'),
        path.join(rootDir, 'resources/js/Pages'),
        // Agrega más directorios donde puedes tener componentes
    ];

    // Posibles extensiones y estructuras de archivos
    const filePatterns = [
        `${componentName}.vue`,
        `${componentName}.jsx`,
        `${componentName}.js`,
        `${componentName}/index.vue`,
        `${componentName}/index.jsx`,
        `${componentName}/index.js`,
    ];

    for (const dir of possibleDirs) {
        for (const pattern of filePatterns) {
            const fullPath = path.join(dir, pattern);
            if (fs.existsSync(fullPath)) {
                return fullPath;
            }
        }
    }

    // Búsqueda recursiva en directorios superiores (hasta cierto límite)
    // Limitamos la búsqueda recursiva para evitar problemas
    if (currentDir !== rootDir && !currentDir.includes('node_modules')) {
        const parentDir = path.dirname(currentDir);
        if (parentDir && parentDir !== currentDir) {
            return findComponentPath(componentName, parentDir);
        }
    }

    return null;
}

async function findVueFiles() {
    try {
        // Usar glob promisificado correctamente
        const files = await glob("./resources/js/Pages/**/*.vue");
        console.log(`Encontrados ${files.length} archivos Vue en Pages`);
        return files;
    } catch (error) {
        console.error("Error buscando archivos Vue:", error);

        // Plan B: buscar archivos manualmente
        console.log("Intentando buscar archivos manualmente...");
        const rootDir = path.join(process.cwd(), 'resources/js/Pages');

        if (!fs.existsSync(rootDir)) {
            console.error(`El directorio no existe: ${rootDir}`);
            console.log("Directorios disponibles en resources/js:",
                fs.existsSync(path.join(process.cwd(), 'resources/js'))
                ? fs.readdirSync(path.join(process.cwd(), 'resources/js'))
                : "resources/js no existe");
            return [];
        }

        // Función recursiva para buscar archivos .vue
        function findVueFilesRecursive(dir, fileList = []) {
            const files = fs.readdirSync(dir);

            files.forEach(file => {
                const filePath = path.join(dir, file);
                if (fs.statSync(filePath).isDirectory()) {
                    findVueFilesRecursive(filePath, fileList);
                } else if (file.endsWith('.vue')) {
                    fileList.push(filePath);
                }
            });

            return fileList;
        }

        return findVueFilesRecursive(rootDir);
    }
}

function countDependencies(componentPath, visited = new Set()) {
    if (!componentsInfo[componentPath] || visited.has(componentPath)) {
        return 0;
    }

    visited.add(componentPath);

    const info = componentsInfo[componentPath];
    let count = info.dependencies.size;

    // No contar dependencias recursivas más de una vez
    info.dependencies.forEach(depName => {
        // Buscar la ruta del componente dependiente
        for (const [path, data] of Object.entries(componentsInfo)) {
            if (data.name === depName && !visited.has(path)) {
                count += countDependencies(path, visited);
            }
        }
    });

    return count;
}

async function main() {
    console.log("Directorio de trabajo actual:", process.cwd());
    console.log("Analizando componentes...");

    // Agregar manualmente componentes recursivos conocidos
    recursiveComponents.add('ContentNode');

    let files = [];
    try {
        files = await findVueFiles();
    } catch (error) {
        console.error("Error crítico al buscar archivos:", error);
        process.exit(1);
    }

    if (files.length === 0) {
        console.log("No se encontraron archivos .vue. Verifica la estructura de tu proyecto.");
        console.log("Intentando listar directorios disponibles:");

        try {
            if (fs.existsSync('./resources')) {
                console.log('Contenido de ./resources:', fs.readdirSync('./resources'));

                if (fs.existsSync('./resources/js')) {
                    console.log('Contenido de ./resources/js:', fs.readdirSync('./resources/js'));
                }
            } else {
                console.log('El directorio ./resources no existe en:', process.cwd());
                console.log('Directorios en la raíz:', fs.readdirSync('./'));
            }
        } catch (error) {
            console.error("Error al listar directorios:", error);
        }
    } else {
        console.log("Archivos .vue encontrados:", files);

        // Análisis principal
        for (const file of files) {
            try {
                // Resetear visitedComponents para cada archivo raíz para asegurar un análisis completo
                visitedComponents.clear();
                await analyzeComponent(file, 0, []);
            } catch (error) {
                console.error(`Error al analizar el componente ${file}:`, error);
            }
        }

        // Calcular información adicional para el informe
        const componentsReport = Object.entries(componentsInfo).map(([path, info]) => {
            const canonicalPath = path;
            const totalDependencies = countDependencies(canonicalPath);
            return {
                path: canonicalPath,
                name: info.name,
                directDependencies: info.dependencies.size,
                recursiveComponents: [...info.recursiveDeps],
                totalDependencies: totalDependencies,
                maxDepth: info.maxChildDepth
            };
        });

        // Ordenar por total de dependencias
        const sortedByDeps = [...componentsReport].sort((a, b) => b.totalDependencies - a.totalDependencies);

        // Ordenar por profundidad
        const sortedByDepth = [...componentsReport].sort((a, b) => b.maxDepth - a.maxDepth);

        console.log("\n=== COMPONENTES MÁS PESADOS (por dependencias totales) ===");
        sortedByDeps.slice(0, 15).forEach((comp, i) => {
            console.log(`${i+1}. ${comp.name} (${comp.path})`);
            console.log(`   Dependencias directas: ${comp.directDependencies}`);
            console.log(`   Dependencias totales: ${comp.totalDependencies}`);
            if (comp.recursiveComponents.length > 0) {
                console.log(`   Contiene componentes recursivos: ${comp.recursiveComponents.join(', ')}`);
            }
        });

        console.log("\n=== COMPONENTES MÁS PROFUNDOS (por nivel de anidamiento) ===");
        sortedByDepth.slice(0, 15).forEach((comp, i) => {
            console.log(`${i+1}. ${comp.name} (${comp.path})`);
            console.log(`   Profundidad máxima: ${comp.maxDepth}`);
            console.log(`   Dependencias totales: ${comp.totalDependencies}`);
        });

        // Generar estadísticas generales
        const allDepths = componentsReport.map(comp => comp.maxDepth).filter(d => d !== undefined);
        const allDeps = componentsReport.map(comp => comp.totalDependencies).filter(d => d !== undefined);

        if (allDepths.length > 0 && allDeps.length > 0) {
            const maxDepth = Math.max(...allDepths);
            const avgDepth = allDepths.reduce((sum, d) => sum + d, 0) / allDepths.length;
            const maxDeps = Math.max(...allDeps);
            const avgDeps = allDeps.reduce((sum, d) => sum + d, 0) / allDeps.length;

            console.log("\n=== ESTADÍSTICAS GENERALES ===");
            console.log(`Total de componentes analizados: ${componentsReport.length}`);
            console.log(`Profundidad máxima: ${maxDepth}`);
            console.log(`Profundidad promedio: ${avgDepth.toFixed(2)}`);
            console.log(`Máximo de dependencias: ${maxDeps}`);
            console.log(`Promedio de dependencias: ${avgDeps.toFixed(2)}`);
            console.log(`Componentes recursivos detectados: ${[...recursiveComponents].join(', ')}`);
        }

        // Generar informe de componentes problemáticos
        const problemComponents = componentsReport.filter(comp =>
            comp.recursiveComponents.length > 0 ||
            comp.totalDependencies > 20 ||
            comp.maxDepth > 10);

        if (problemComponents.length > 0) {
            console.log("\n=== COMPONENTES POTENCIALMENTE PROBLEMÁTICOS ===");
            problemComponents.forEach((comp, i) => {
                console.log(`${i+1}. ${comp.name} (${comp.path})`);
                if (comp.recursiveComponents.length > 0) {
                    console.log(`   ⚠️ Contiene componentes recursivos: ${comp.recursiveComponents.join(', ')}`);
                }
                if (comp.totalDependencies > 20) {
                    console.log(`   ⚠️ Demasiadas dependencias: ${comp.totalDependencies}`);
                }
                if (comp.maxDepth > 10) {
                    console.log(`   ⚠️ Árbol de componentes muy profundo: ${comp.maxDepth}`);
                }
            });
        }
    }
}

main().then(() => {
    console.log("\nAnálisis completado con éxito!");
}).catch(err => {
    console.error("Error crítico en el análisis:", err);
    process.exit(1);
});
