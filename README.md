# Asoges

**Gestión Integral y Documental de Asociaciones**

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.4-blue)](https://php.net)
[![License](https://img.shields.io/badge/License-CC%20BY--SA%203.0-green)](https://creativecommons.org/licenses/by-sa/3.0/es/)

**Asoges** es una plataforma web desarrollada para centralizar y modernizar la gestión administrativa de asociaciones y clubes. Su objetivo es sustituir la dispersión de hojas de cálculo y documentos físicos por un sistema unificado que garantice la trazabilidad y la transparencia.

La aplicación permite la gestión coordinada de cinco libros esenciales (Socios, Actas, Documentación, Contabilidad e Inventario) bajo una arquitectura **multiasociación**, permitiendo que una única instancia gobierne varias entidades con aislamiento de datos.

## 🎓 Origen del Proyecto

Este desarrollo tiene su origen en el **Trabajo de Final de Grado (TFG)** del Grado de Ingeniería Informática.

La memoria completa del proyecto, que incluye el análisis, diseño y detalles de implementación, se encuentra alojada en el repositorio institucional O2 de la UOC:
👉 **[Consultar Memoria del Proyecto](https://hdl.handle.net/10609/154025)**

## 🚀 Características Principales

El sistema cubre el siguiente alcance funcional:

-   **👥 Libro de Socios:** Gestión de altas, bajas y modificaciones con histórico de participación.
-   **📝 Libro de Actas:** Almacenamiento de actas en PDF con repositorio histórico y búsquedas avanzadas.
-   **📂 Registro Documental:** Control de entrada y salida de documentación oficial con trazabilidad.
-   **💰 Libro de Contabilidad:** Registro de ingresos y gastos, con generación de informes y exportación.
-   **📦 Libro de Inventario:** Control del patrimonio material de la asociación.
-   **🔐 Multitenancy:** Gestión de múltiples asociaciones con roles y permisos diferenciados (Superadministrador, Presidente, Secretario, Tesorero).
-   **📊 Exportación:** Capacidad de exportar datos a Excel para auditorías o informes externos.

## 🛠️ Stack Tecnológico

El proyecto está construido sobre un stack moderno y robusto:

-   **Backend Framework:** [Laravel 12.19.3](https://laravel.com)
-   **Panel Administrativo:** [Filament](https://filamentphp.com).
-   **Lenguaje:** PHP 8.4.8
-   **Base de Datos:** MySQL 8.0.42

## 📥 Instalación y Despliegue

Sigue estos pasos para levantar el proyecto en tu entorno local:

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/domingoruiz/asoges.git
    cd asoges
    ```

2.  **Configurar variables de entorno:**
    ```bash
    cp .env.example .env
    # Configura tus credenciales de base de datos en el archivo .env
    ```

3.  **Instalar dependencias:**
    ```bash
    composer update
    ```

4.  **Ejecutar migraciones y seeders:**
    Para una instalación limpia con datos de prueba:
    ```bash
    php artisan migrate:fresh --seed
    ```

## 💻 Comandos Útiles

A continuación se listan comandos frecuentes para el mantenimiento de la aplicación:

**Actualizar dependencias:**
```bash
composer update
```

**Ejecutar migraciones pendientes:**
```bash
php artisan migrate
php artisan db:seed
```

**Limpieza profunda de caché:**
```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan event:clear && php artisan clear-compiled && php artisan optimize
```

## 👤 Desarrollo

Aplicación desarrollada por Domingo Ruiz Arroyo
<[ordenadordomi@gmail.com](mailto:ordenadordomi@gmail.com)>

## 📄 Licencia

Este proyecto está sujeto a una licencia **Reconocimiento-CompartirIgual 3.0 España de Creative Commons (CC BY-SA 3.0 ES)**. Consulte el archivo [LICENSE](../main/LICENSE) para obtener más detalles.