# Memory Policy (Engram)

Reglas para el uso de la memoria persistente para evitar el caos y mantener un contexto de alta calidad.

## Qué Guardar (Prioridad Alta)
- **Decisiones de Arquitectura**: Por qué se eligió una estructura o tecnología específica.
- **Módulos Creados**: Fecha de creación, propósito central y archivos clave.
- **Patrones Reutilizables**: Formas estándar de resolver problemas comunes (ej. modales, validación de DNI).
- **Errores Detectados (Bugfixes)**: Qué falló, por qué, y cómo se arregló para no repetirlo.
- **Preferencias del Usuario**: Estilos visuales, idiomas, o formas de trabajo específicas.

## Qué NO Guardar
- **Prompts Temporales**: Consultas de "cómo se hace X" que no resultan en cambios permanentes.
- **Logs de Depuración**: Salidas de terminal o errores de sintaxis que se corrigen en el momento.
- **Pruebas Ad-hoc**: Scripts creados en `.tmp/` para validaciones rápidas de una sola vez.

## Formato Obligatorio
Siempre usar el formato de `mem_save`:
- **What**: Qué se hizo.
- **Why**: Por qué (motivo técnico o pedido de usuario).
- **Where**: Archivos afectados.
- **Learned**: Aprendizajes clave para el futuro.
