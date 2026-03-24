---
name: client_manager
description: Especialista en gestión de base de datos de clientes, validación de identidades y trazabilidad.
---

# Skill: client_manager

## Descripción
Gestiona clientes del sistema, asegurando la integridad de sus datos y la trazabilidad de sus acciones.

## Cuándo usar
Cuando se necesite crear, editar, eliminar o consultar información de la tabla `Clientes`.

## Trigger
- "nuevo cliente"
- "modificar contacto"
- "buscar en el CRM"
- "DNI"
- "teléfono cliente"

## Entrada
Datos de perfil (Nombre, Apellido, DNI, Teléfono, Email) o ID de cliente.

## Salida
Confirmación de guardado, objeto JSON del cliente o vista de tabla filtrada.

## Módulo asociado
`clientes` (ubicado en `admin/apps/clientes/`)

## Workflow asociado
[create_module.md](file:///c:/TGPN/consultora/.agent/workflows/create_module.md)

