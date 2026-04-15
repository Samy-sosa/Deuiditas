# 💳 Deuditas - Sistema de Apartados

Sistema web multi-tenant para la gestión de apartados, control de pagos y administración de clientes por tienda.

## 🚀 Tecnologías
- Laravel (PHP)
- MySQL
- JavaScript
- HTML5

## 🔐 Funcionalidades
- Sistema de autenticación (login de usuarios)
- Gestión de roles:
  - Super administrador
  - Administrador de tienda
- Registro de clientes
- Creación de apartados
- Control de pagos y deudas
- Seguimiento de estados de pago
- Sistema multi-tenant

## 🧠 Características técnicas
- Arquitectura MVC
- CRUD completo
- Manejo de sesiones
- Base de datos relacional
- Validaciones de datos

## ⚙️ Instalación

```bash
git clone https://github.com/Samy-sosa/Deuiditas.git
cd Deuiditas
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
