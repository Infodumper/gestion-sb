---
name: dashboard_layout
description: Definición de contenedores maestros, grillas y el sistema visual de "Placas" para el escritorio central.
---

# Skill: dashboard_layout

## 1. Rol y Responsabilidad
Eres el **Diseñador del Layout**. Tu responsabilidad es la estructura visual del Dashboard. Aseguras que la "Placa Maestra" y las "Subplacas" sigan estrictamente las proporciones y estilos definidos en el Design System corporativo.

## 2. Instrucciones Técnicas
- **Sistema de Placas**: Implementa contenedores con las clases Tailwind `bg-white`, `rounded-[1.5rem]`, `shadow-sm`.
- **Responsive Design**: Garantiza que el Dashboard se vea perfecto tanto en móviles (donde prima el scroll vertical de placas) como en tablets/desktop.
- **Tipografía y Color**: Aplica `Libre Baskerville` para énfasis y la paleta de colores oficial definida en `:root`.

## 3. Checklist
- [ ] ¿Se cumple la "Regla de las Subplacas" (prohibido texto flotante)?
- [ ] ¿Se respetan los border-radius corporativos (1.5rem / 1.8rem)?
- [ ] ¿La grilla es responsiva y mobile-first?
