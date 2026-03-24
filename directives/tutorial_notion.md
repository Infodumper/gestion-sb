# Tutorial: Sincronización de Documentación con Notion

Esta guía explica cómo se configuró el sistema para que las directivas locales se sincronicen automáticamente con el **Espacio de Trabajo de Notion**.

## 1. Conceptos Clave
El sistema utiliza la **API de Notion** para actuar como un puente. En lugar de copiar y pegar, el Agente lee los archivos `.md` (Markdown) y los convierte en "Bloques" de Notion (títulos, listas, párrafos).

## 2. Configuración Inicial (El Handshake)
Para que esto funcione, se realizaron tres pasos críticos:
*   **Creación de la Integración**: Se generó un *Secret Token* en `developers.notion.com`. Este token es la "llave" que permite al código entrar a Notion.
*   **Identificación del Destino**: Cada página de Notion tiene un ID único (los 32 caracteres al final de la URL).
*   **Permisos de Conexión**: Notion es privado por defecto. Se debió invitar a la integración a la página mediante el menú de "Add connections" (...) en la esquina superior derecha.

## 3. Implementación Técnica
El núcleo de esta función vive en:
*   `includes/notion_bridge.php`: Un motor en PHP que traduce el texto simple a objetos que la API de Notion entiende.
*   **Parsing de Bloques**: El sistema identifica si una línea empieza con `#`, `##` o `*` para decidir si crear un título o una viñeta en Notion.

## 4. Estructura de la Biblioteca
Se diseñó una jerarquía para mantener el orden:
1.  **Raíz (Biblioteca)**: El contenedor principal.
2.  **Categorías (Directivas)**: Sub-páginas que agrupan documentos por tipo.
3.  **Documentos (Páginas)**: El contenido final de cada archivo local.

## 5. Cómo Actualizar en el Futuro
Cada vez que se cree una nueva directiva importante o se modifique una existente, el Agente puede ejecutar el comando de sincronización masiva para asegurar que la "Nube" siempre sea un reflejo fiel de la "Realidad Local".

---
*Este tutorial fue generado y sincronizado automáticamente por el Agente Antigravity.*
