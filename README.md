# Backend de Laravel - API de Autenticación y Roles

Este es el backend de una aplicación construida con **Laravel** que maneja la **autenticación de usuarios** utilizando **Sanctum** y la asignación de **roles y permisos**. Este backend permite a los usuarios registrarse, iniciar sesión, obtener sus permisos y realizar acciones basadas en su rol.

## Requisitos

- PHP >= 8.0
- Laravel 10
- MySQL o cualquier otra base de datos compatible con Laravel
- Composer

## Instalación

1. Clona este repositorio:
    ```bash
    git clone https://github.com/yeltsinhp/pro20_rt.git
    ```

2. Navega al directorio del proyecto:
    ```bash
    cd pro20_rt
    ```

3. Instala las dependencias de PHP:
    ```bash
    composer install
    ```

4. Copia el archivo `.env.example` a `.env`:
    ```bash
    cp .env.example .env
    ```

5. Configura tu base de datos en el archivo `.env`:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nombre_de_tu_base_de_datos
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña
    ```

6. **Si las migraciones no funcionan correctamente, puedes ejecutarlas una por una.**

    Para ejecutar las migraciones una por una, usa los siguientes comandos para cada migración:
    ```bash
    php artisan migrate --path=/database/migrations/2025_06_10_050712_create_roles_table.php
    php artisan migrate --path=/database/migrations/2025_06_10_050713_create_permissions_table.php
    php artisan migrate --path=/database/migrations/2025_06_10_050714_create_role_permission_table.php
    ```

7. Inicia el servidor de desarrollo de Laravel:
    ```bash
    php artisan serve
    ```

---

## Endpoints

### 1. **POST /api/register**

#### Descripción:
Crea un nuevo usuario. El usuario debe proporcionar su nombre, correo electrónico, contraseña y rol.

#### Body:

```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "estudiante"
}
