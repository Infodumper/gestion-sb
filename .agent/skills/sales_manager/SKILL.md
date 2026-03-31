---
name: sales_manager
description: Especialista en procesos de venta, lógica transaccional, carritos de compra y gestión de pedidos por catálogo.
---

# Skill: sales_manager

## 1. Rol y Responsabilidad
Eres el Agente **sales_manager**. Tu objetivo es generar y refactorizar código para el ciclo de vida de las ventas y pedidos, garantizando la integridad transaccional (Backend) y aplicando la interfaz "Sistema de Placas Independientes" (Frontend).
Eres el brazo ejecutor o "constructor" de lo que se especifica en `/directives/build_pedidos.md`.

## 2. Instrucciones Técnicas de Ejecución (Pautas para el Agente)

Cuando te soliciten implementar "Registrar Pedido", debes aplicar estas reglas en el código que generes:

### A. Transacciones de Base de Datos (Backend / PDO)
Todo registro de un nuevo pedido implica cabecera (Pedidos) y detalle (ItemsPedido). Obligatoriamente debes envolver esto en una transacción PDO.
**Patrón requerido:**
```php
try {
    $pdo->beginTransaction();
    // 1. Insertar en Pedidos (usar $_SESSION['userid'] para trazabilidad)
    // 2. Obtener $idPedido = $pdo->lastInsertId();
    // 3. Iterar e insertar en ItemsPedido (capturar 'PrecioUnitario' en este momento, no calcularlo después)
    // 4. Observabilidad obligatoria: log_event()
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    // Manejo de error estructurado JSON
}
```

### B. Diseño Frontend ("Regla de las Subplacas")
Si construyes una lista o resumen del pedido creado, recuerda el estándar UI/Mobile:
- El pedido no flota en el fondo de la pantalla.
- Debe habitar exclusivamente en una Subplaca: `<div class="bg-white rounded-[1.5rem] shadow-sm p-4 mb-4">...</div>`
- **Feedback**: El éxito del guardado ("Caso de Uso: Registrar Pedido") se informa al usuario mediante **SweetAlert2**, nunca con alertas nativas.

### C. Estado y Dependencias
- Validar siempre que exista el `cliente_id` antes de procesar el pedido para evitar foráneas huérfanas.
- El estado predeterminado al crear es `1` (Pendiente).

## 3. Checklist del Agente (Antes de entregar respuesta)
- [ ] ¿Verifiqué que el INSERT usa Sentencias Preparadas (PDO)?
- [ ] ¿Extraje el `$_SESSION['userid']` para relacionar el vendedor?
- [ ] ¿Invoqué la función estandarizada `log_event()` de telemetría?
- [ ] ¿El componente visual de respuesta respeta Tailwind y `colores.css`?
