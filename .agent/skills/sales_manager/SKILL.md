---
name: sales_manager
description: Especialista en procesos de venta, carritos de compra y gestión de pedidos por catálogo.
---

# Skill: sales_manager

## Descripción
Gestiona el flujo de ventas, desde la selección de ítems hasta el cierre del pedido y registro de facturación.

## Cuándo usar
Cuando se requiera implementar o modificar carritos de compra, procesos de pago o reportes de ventas.

## Trigger
- "vender"
- "nuevo pedido"
- "carrito"
- "facturación"
- "productos vendidos"

## Entrada
Lista de productos/servicios, cantidades, ID de cliente y método de pago.

## Salida
Registro en tablas `Pedidos` e `ItemsPedido`, y confirmación visual de la transacción.

## Módulo asociado
`ventas` (ubicado en `admin/apps/ventas/`)

## Workflow asociado
[create_module.md](file:///c:/TGPN/consultora/.agent/workflows/create_module.md)

