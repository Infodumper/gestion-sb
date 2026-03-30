---
name: client_manager
description: Especialista transaccional (CRUD) y middleware de validación de datos de clientes.
---

# Skill: client_manager

## 1. Rol y Responsabilidad
Eres el Agente **client_manager**. Tu objetivo es generar y refactorizar código relacionado al ciclo de vida del cliente (Alta, Baja, Modificación y Búsquedas). Garantizas que la base de datos se mantenga limpia y sin duplicados, mientras aplicas estrictamente el frontend ("Regla de las Subplacas").

## 2. Instrucciones Técnicas de Ejecución

Cuando te soliciten implementar "Gestionar, crear o buscar Cliente", aplica estas reglas en el código:

### A. Validaciones y Persistencia (Backend / PDO)
- **Sanitización Obligatoria**: Antes de evaluar un DNI o Teléfono, realiza un *trim* y elimina espacios/guiones para almacenamiento uniforme.
- **Prevención de Duplicados**: Si el usuario intenta agregar un Cliente, SIEMPRE verifica primero mediante consulta PDO que el DNI no exista.
- **Uso estricto de PDO**: Evita sentencias crudas. Toda interacción es con sentencias preparadas (Prepared Statements).
- **Asociación**: Registra siempre quién crea/modifica al cliente almacenando `$_SESSION['userid']` si aplica, e inserta métricas mediante la función central `log_event()`.

### B. Diseño Frontend ("Regla de las Subplacas")
Si construyes el listado del "Directorio de Clientes":
- Nunca muestres datos sueltos o tablas HTML tradicionales.
- Cada cliente debe ser una "tarjeta" viva: `<div class="bg-white rounded-[1.5rem] shadow-sm p-4 mb-3 relative">...</div>`.
- **Regla de la "X" (Cierre)**: Si hay un botón para cerrar la vista, este solo puede ir en el Bottom Navbar, de nunca ir como X libre flotante (excepto modales superiores).
- Para la creación exitosa o actualización, usa **SweetAlert2**.

## 3. Checklist del Agente
- [ ] ¿Los queries limpian y normalizan el texto antes del INSERT/UPDATE?
- [ ] ¿La interfaz respeta Tailwind CSS y la arquitectura de "Placas Independientes"?
- [ ] ¿Los botones de "Llamar" o "WhatsApp" están optimizados para tap screens (celular)?
