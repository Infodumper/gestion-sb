# 🚀 Guía Maestra de GitHub y Branches (SSH Edition)

¡Excelente iniciativa! Dominar las "branches" (ramas) es lo que separa a un programador que "pica código" de un **desarrollador profesional**. 

Aquí tienes la guía definitiva actualizada para el entorno de **Antigravity**.

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

---

## 2. El Ciclo de Vida del Trabajo (Workflow SSL/SSH)

Ya tenemos configurado **SSH** para mayor seguridad y comodidad (sin pedir contraseñas). El proceso que seguiremos a partir de ahora es:

1.  **Estar en `main`:** Asegurarte de tener lo último del "tronco".
2.  **Crear una rama:** `git checkout -b feature-mi-idea` (creas un mundo paralelo).
3.  **Trabajar:** Haces tus cambios, guardas, comiteas (`git commit`).
4.  **Subir al cielo (GitHub):** `git push origin feature-mi-idea`.
5.  **Crear el PR:** Vas a GitHub y pulsas el botón verde "Compare & pull request".
6.  **Merge (Mezclar):** Una vez aprobado, se integra en `main` y se borra la ramita.

---

## 3. Comandos que usarás el 90% del tiempo

| Acción | Comando |
| :--- | :--- |
| **Ver en qué rama estás** | `git branch` |
| **Crear y saltar a una rama nueva** | `git checkout -b nombre-rama` |
| **Saltar a una rama que ya existe** | `git checkout nombre-rama` |
| **Borrar una rama (con cuidado)** | `git branch -d nombre-rama` |
| **Ver qué archivos cambiaste** | `git status` |
| **Guardar cambios localmente** | `git add .` seguido de `git commit -m "Explicación"` |
| **Actualizar el remoto (SSH)** | `git push origin nombre-rama` |

---

## 💡 Configuración SSH del Proyecto (Actualizado)

Si por alguna razón necesitas reconectar el repositorio, usa el comando SSH:
`git remote set-url origin git@github.com:Infodumper/gestion-sb.git`

Esto asegura que la comunicación entre tu PC y GitHub sea cifrada y automática.

¿Te gustaría que trabajemos en una rama nueva para la siguiente funcionalidad?
