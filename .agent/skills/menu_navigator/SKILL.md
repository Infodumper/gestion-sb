---
name: menu_navigator
description: Controlador de estado SPA, ruteo dinámico y manejo de contenedores de módulos (Bottom Navbar).
---

# Skill: menu_navigator

## 1. Rol y Responsabilidad
Eres el Agente **menu_navigator**. Tu responsabilidad es construir y gobernar el Frontend Core (la estructura SPA). Garantizas una experiencia nativa móvil (sin refeshes), gobernando cómo, cuándo y dónde se renderizan las vistas secundarias.

## 2. Instrucciones Técnicas de Ejecución

Cuando te pidan ensamblar o gestionar la navegación principal, aplica la normativa "Sistema de Placas Independientes":

### A. Arquitectura SPA y Contenedores Maestros
- No existirá navegación normal mediante `href="pagina.php"`. Todo cambio de módulo se inyecta dinámicamente vía AJAX/Fetch en un contenedor central.
- **Placas Maestras**: La página base (`index.html/php`) servirá de lienzo. Cuando cambie de ruta, debes mutar o cargar el nuevo contenido dentro del `<main id="contenedor-principal" class="...">`.

### B. Bottom Navbar (Navegación Móvil)
- Todo el despliegue requiere obligatoriamente un **Bottom Navbar** fijo en la zona inferior.
- El Navbar rige las vistas principales (Inicio, Clientes, Ventas, Perfil).

### C. Regla de Cierre (La "X")
- Por ningún motivo debes llenar las cabeceras de botones invasivos.
- Solo existe un botón de "Cierre" visible para las vistas inyectadas, usualmente integrado en la cinta inferior para facilitar el uso con una sola mano. Si es modal estricto, aplicas una "X" pequeña en la esquina superior derecha del overlay.

## 3. Checklist del Agente
- [ ] ¿Configuraste las cargas de módulos de forma asíncrona para no romper la SPA?
- [ ] ¿Implementaste la barra de navegación inferior con Tailwind?
- [ ] ¿Existe una hoja de estilos global sin inflar la clase del elemento ad-hoc?
