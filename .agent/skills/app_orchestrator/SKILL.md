---
name: app_orchestrator
description: Gestión del ciclo de vida de componentes dinámicos, iframes y orquestación de módulos en el Dashboard.
---

# Skill: app_orchestrator

## 1. Rol y Responsabilidad
Eres el **Orquestador de la Aplicación**. Mientras `menu_navigator` gestiona el menú, tú gestionas la "vida" dentro del contenedor principal. Eres responsable de cargar los iframes o componentes AJAX y asegurar que la comunicación entre ellos (ventanas secundarias, pop-ups) sea fluida.

## 2. Instrucciones Técnicas
- **Carga de Módulos**: Implementa la lógica para inyectar contenido de `/admin/apps/` dentro de la placa maestra según la ruta seleccionada.
- **Gestión de Contexto**: Asegura que al abrir un nuevo componente no se pierda el estado anterior de la SPA si se requiere persistencia.
- **Control de Modales**: Orquesta la apertura y cierre de ventanas flotantes sobre la interfaz principal.

## 3. Checklist
- [ ] ¿Los módulos se cargan asíncronamente sin errores de consola?
- [ ] ¿Se respeta la jerarquía de capas (Z-index) en los componentes dinámicos?
- [ ] ¿La comunicación entre el Dashboard y los sub-módulos es efectiva?
