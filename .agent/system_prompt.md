# Agent Bootstrap

Este proyecto usa arquitectura basada en skills.

Proceso obligatorio:

1. cargar contexto mediante context_loader.md
2. Antes de crear un nuevo módulo, consultar module_registry.md para verificar si el módulo ya existe.
3. Antes de crear consultas SQL o formularios, consultar data_contracts.md, y no inventar campos ni modificar estructuras de datos.
4. leer architecture_registry.md
5. leer skills_registry.md
6. identificar skill adecuada
7. leer directiva correspondiente
8. ejecutar implementación

Reglas:

- no crear archivos fuera de la arquitectura
- no ejecutar acciones sin skill
- seguir directivas estrictamente