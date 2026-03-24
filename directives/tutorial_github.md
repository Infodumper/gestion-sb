# 🚀 Guía Maestra de GitHub y Branches

¡Excelente iniciativa! Dominar las "branches" (ramas) es lo que separa a un programador que "pica código" de un **desarrollador profesional**. 

Aquí tienes la guía definitiva en español para el entorno de **Antigravity**.

---

## 1. Conceptos Fundamentales

### 🌿 ¿Ramas o Branches?
**Sí, son exactamente lo mismo.** "Branch" es el término técnico en inglés y "Rama" es la traducción al español.

Imagina que tu código es un árbol:
- **`main` (o `master`):** Es el tronco. Aquí vive el código que funciona perfectamente y está listo para ser usado (producción).
- **`feature-x`:** Es una rama que sale del tronco. Aquí puedes experimentar, romper cosas y probar ideas sin que el tronco se vea afectado.

### 🔄 Pull Request (PR)
Un **Pull Request** no es un comando de Git, es una funcionalidad de **GitHub**. 
Es una **propuesta de cambio**. Cuando terminas tu trabajo en una rama (por ejemplo, `feature-nuevo-login`), "pides" permiso para integrar ese código en la rama `main`.
- Permite revisar el código antes de mezclarlo.
- Sirve como registro de qué se hizo y por qué.
- Es el lugar ideal para que la IA (como yo) revise tu código antes de que llegue a producción.

---

## 2. El Ciclo de Vida del Trabajo (Workflow)

Este es el proceso que seguiremos a partir de ahora:

1.  **Estar en `main`:** Asegurarte de tener lo último del "tronco".
2.  **Crear una rama:** `git checkout -b feature-mi-idea` (creas un mundo paralelo).
3.  **Trabajar:** Haces tus cambios, guardas, comiteas (`git commit`).
4.  **Subir al cielo (GitHub):** `git push origin feature-mi-idea`.
5.  **Crear el PR:** Vas a GitHub y pulsas el botón verde "Compare & pull request".
6.  **Merge (Mezclar):** Una vez aprobado, se integra en `main` y se borra la ramita.

---

## 3. Preparación Inicial (Pasos a seguir)

Actualmente, el proyecto `consultora` **no tiene Git inicializado**. Para empezar, deberías seguir estos pasos:

### Paso A: Inicializar Git Local
En la terminal de VS Code:
```powershell
git init
git add .
git commit -m "Initial commit: Proyecto base"
```

### Paso B: Conectar con GitHub
1. Crea un repositorio en [GitHub.com](https://github.com) llamado `consultora`.
2. Copia la URL del repo y pégala aquí:
```powershell
git remote add origin https://github.com/TU_USUARIO/consultora.git
git branch -M main
git push -u origin main
```

---

## 4. Comandos que usarás el 90% del tiempo

| Acción | Comando |
| :--- | :--- |
| **Ver en qué rama estás** | `git branch` |
| **Crear y saltar a una rama nueva** | `git checkout -b nombre-rama` |
| **Saltar a una rama que ya existe** | `git checkout nombre-rama` |
| **Borrar una rama (con cuidado)** | `git branch -d nombre-rama` |
| **Ver qué archivos cambiaste** | `git status` |

---

## 💡 Recomendación de Antigravity
Cuando trabajemos juntos en una tarea (por ejemplo, corregir la base de datos), **siempre** te sugeriré:
> "Che, ¿creamos una rama `fix-db` para trabajar esto seguro?"

De esa forma, si algo sale mal, simplemente borramos la rama y el `main` sigue intacto.

¿Te gustaría que te ayude a inicializar el repositorio ahora mismo?
