# Coding Conventions

Estándares de desarrollo para asegurar la coherencia en todos los módulos generados por la Software Factory.

## Estilo de Código
- **Nomenclatura**: Usar `snake_case` para nombres de archivos, variables y campos de base de datos.
- **Idioma**: Todo el código de cara al usuario (UI) y comentarios explicativos deben estar en **Español**. Nombres de variables técnicos pueden ser en inglés si es el estándar de la librería.
- **Indentación**: 4 espacios (o tabuladores consistentes con el archivo existente).

## Arquitectura y Lógica
- **Separación de Capas**: Separar la lógica de negocio (Services/Actions) de las vistas (Vistas/Partial).
- **No SQL en Vistas**: Las consultas a la base de datos deben estar en archivos `ajax_*.php` o controladores, nunca directamente en el HTML de la vista.
- **Validación de Datos**: Toda entrada de usuario debe ser saneada antes de usarse en una query SQL (usar Prepared Statements con PDO).

## UI / UX (Tailwind)
- **Modo Oscuro/Claro**: Usar clases de Tailwind que soporten ambos modos.
- **Componentes**: Reutilizar `partials/` para elementos comunes como modales, headers y footers.
- **Feedback**: Usar SweetAlert2 o notificaciones Toast para confirmar acciones del usuario.
