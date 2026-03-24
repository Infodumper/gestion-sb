# Architecture Registry

Este archivo describe la arquitectura del sistema.

Los agentes deben leer este archivo antes de crear
o modificar componentes del proyecto.

---

# Arquitectura General

El sistema sigue una arquitectura modular.

Las aplicaciones viven en:

admin/apps/

Cada módulo es independiente.

---

# Componentes principales

## Admin Panel

Ubicación

admin/

Responsabilidad

Interfaz administrativa del sistema.

Contiene:

- módulos funcionales
- dashboards
- herramientas SQL

---

## Applications

Ubicación

admin/apps/

Cada aplicación representa un módulo del sistema.

Ejemplos

servicios (Principal)
productos
pedidos
clientes (Secundario/Último)
ventas

Cada módulo debe contener:

- interfaz principal
- endpoints AJAX
- utilidades

---

## Database Layer

Ubicación

admin/apps/sql/

Responsabilidad

- conexión a base de datos
- ejecución de consultas
- inspección de tablas

Archivo principal

conexion.php

---

## Utilities

Ubicación

utils/

Responsabilidad

- logging
- funciones reutilizables
- utilidades del sistema

---

## Logs

Ubicación

logs/

Responsabilidad

registro de operaciones del sistema.

---

# Convenciones del Proyecto

Reglas obligatorias:

1. cada módulo vive en admin/apps/
2. endpoints dinámicos deben ser AJAX
3. operaciones críticas deben registrarse
4. evitar duplicar lógica

---

# Flujo de Desarrollo

Cuando se solicita una nueva funcionalidad:

1 identificar skill en skills_registry.md  
2 leer directiva correspondiente  
3 crear módulo en admin/apps  
4 registrar cambios