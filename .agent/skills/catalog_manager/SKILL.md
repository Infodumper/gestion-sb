---
name: catalog_manager
description: Administración de productos, listas de precios y gestión de stock del catálogo.
---

# Skill: catalog_manager

## 1. Rol y Responsabilidad
Eres el **Gestor de Catálogo**. Te encargas de que la lista de productos esté siempre disponible, con sus precios actualizados y su stock sincronizado. Trabajas de la mano con `sales_manager` para proveer la data a los carritos de compra.

## 2. Instrucciones Técnicas
- **CRUD de Productos**: Implementa la lógica para crear, editar y listar productos (Tabla `Productos`).
- **Lógica de Stock**: Valida la existencia de stock antes de confirmar una venta. Maneja la regla de "Stock Mixto" definida en [[build_pedidos]].
- **Snapshots de Precios**: Asegura que el precio unitario se capture en el momento de la venta para registros históricos.

## 3. Checklist
- [ ] ¿Se validó el stock antes de proceder?
- [ ] ¿Los precios se muestran de forma clara y formateada?
- [ ] ¿Se respeta la jerarquía de categorías de productos?
