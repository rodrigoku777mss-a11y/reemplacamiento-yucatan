
# Portal Web de Reemplacamiento Vehicular de Yucatán


## Integrantes del equipo
* Rodrigo de Jesús Ku Mis
* Alan Andrés Canché Moo

---

## Requisitos previos
* **XAMPP** instalado.
* **Git** instalado en el equipo.
* Un navegador web y un editor de código como **Visual Studio Code**.

---

## Instrucciones completas de instalación y ejecución local

### 1. Activar XAMPP localmente (Apache y MariaDB)
1. Abre el **Panel de Control de XAMPP** en tu computadora.
2. En la fila de **Apache**, haz clic en el botón **Start**
3. En la fila de **MySQL** (que en XAMPP corresponde a **MariaDB**), haz clic en el botón **Start**
4. Mantén abierta o minimizada la ventana de XAMPP mientras usas la aplicación.

### 2. Clonar el repositorio desde `htdocs`
1. Abre el Explorador de archivos y ve a la carpeta `htdocs` de tu instalación de XAMPP (por ejemplo: `C:\xampp\htdocs`).
2. Abre una terminal (PowerShell, CMD o Git Bash) ubicada dentro de `C:\xampp\htdocs`.
3. Ejecuta el siguiente comando para clonar el repositorio:

git clone https://github.com/rodrigoku777mss-a11y/reemplacamiento-yucatan.git

4. Espera a que termine la descarga y verifica que se haya creado la carpeta `reemplacamiento-yucatan` dentro de `htdocs` (la ruta final quedará como `C:\xampp\htdocs\reemplacamiento-yucatan`).

### 3. Abrir phpMyAdmin y crear/importar la base de datos en SQL
1. Asegúrate de que **Apache** y **MySQL (MariaDB)** estén activos en XAMPP.
2. Abre tu navegador web y entra a:

http://localhost/phpmyadmin

3. En el menú superior de phpMyAdmin, haz clic en la pestaña **SQL**.
4. Ve a la carpeta del proyecto (`reemplacamiento-yucatan/database/`) y abre el archivo **`reemplacamiento.sql`** con un editor de texto o Visual Studio Code.
5. Selecciona **todo** el contenido de `reemplacamiento.sql` (`Ctrl + A`) y cópialo (`Ctrl + C`).
6. Regresa a la pestaña **SQL** de phpMyAdmin, pega (`Ctrl + V`) todo el código SQL dentro del recuadro de consulta y haz clic en **Continuar** para ejecutarlo.
7. Revisa el panel izquierdo de phpMyAdmin y confirma que se haya creado la base de datos **`reemplacamiento`** junto con sus **7 tablas**:
   * `USUARIOS`
   * `PROPIETARIO`
   * `MODELO`
   * `VEHICULO`
   * `TRAMITE`
   * `MODULO`
   * `CITA`
   junto con los datos iniciales cargados.

### 4. Verificar la configuración de conexión a la base de datos
Abre el archivo `config/conexion.php` y verifica que los parámetros correspondan a tu entorno local de XAMPP:
* **Host:** `localhost`
* **Base de datos:** `reemplacamiento`
* **Usuario:** `root`
* **Contraseña:** `""` (vacía por defecto en XAMPP)

### 5. Abrir y probar el sitio en el navegador
1. Abre una pestaña nueva en tu navegador y entra a:

http://localhost/reemplacamiento-yucatan/index.php

2. Inicia sesión con alguna de las cuentas de prueba o crea una cuenta nueva desde **Regístrate como ciudadano**.

---

## Credenciales de prueba incluidas en el script SQL

| Rol | Correo electrónico | Contraseña |
| **Administrador** | `admin@yucatan.gob.mx` | `password` |
| **Ciudadano** | `juan.perez@correo.com` | `password` |

---

