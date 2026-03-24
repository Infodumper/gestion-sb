# Directiva de Pruebas Automáticas - Proyecto Antigravity

Esta directiva define los estándares y procedimientos para la implementación de pruebas en el sistema Consultora.

## 1. Niveles de Pruebas

Se implementarán cuatro capas de pruebas para asegurar la calidad y estabilidad del sistema:

### A. Pruebas Unitarias (`tests/unit`)
- **Propósito**: Probar funciones individuales de forma aislada.
- **Enfoque**: Lógica de cálculos, transformaciones de datos, validaciones.
- **Herramienta**: PHPUnit / Python unittest.

### B. Pruebas de Base de Datos (`tests/database`)
- **Propósito**: Validar queries, integridad y CRUD.
- **Enfoque**: Verificación de registros después de inserciones, joins complejos, reportes SQL.
- **Herramienta**: PHP nativo / Python scripts.

### C. Pruebas de API / Backend (`tests/api`)
- **Propósito**: Validar endpoints y respuestas del servidor.
- **Enfoque**: Login AJAX, guardado de pedidos, exportación de CSV.
- **Herramienta**: cURL / Guzzle / Python requests.

### D. Pruebas End-to-End (`tests/e2e`)
- **Propósito**: Simular flujos de usuario reales en el navegador.
- **Enfoque**: Proceso completo de login -> creación de pedido -> verificación.
- **Herramienta**: Playwright.

## 2. Estructura de Archivos
```
consultora/
├── tests/
│   ├── unit/         # Lógica pura
│   ├── database/     # Integridad SQL
│   ├── api/          # Endpoints backend
│   └── e2e/          # Playwright flows
```

## 3. Pruebas Críticas (MVP)
Todo cambio importante debe validar al menos:
1. **Login Funciona**: El usuario puede entrar con credenciales válidas y es rechazado con inválidas.
2. **Crear Pedido/Nota Funciona**: El sistema permite guardar servicios asignados a un cliente.
3. **Exportar CSV Funciona**: La generación de archivos es correcta y contiene los datos esperados.

## 4. Ejecución
Las pruebas deben poder ejecutarse de forma centralizada:
- `php tests/run_tests.php`: Ejecutor rápido de integridad.
- `npx playwright test`: (Si aplica) Pruebas de navegador.

## 5. Reporte
Los resultados de las pruebas deben ser visibles en el Dashboard de Antigravity (Capa 4) mediante logs en la base de datos `antigravity.db`.
