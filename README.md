<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

### Despliegue de Aitrol

- Sirve únicamente el directorio `public/`. Usa un certificado TLS válido y configura el proxy o Apache para redirigir HTTP y rechazar solicitudes HTTP que lleven credenciales. El proceso de PHP debe poder escribir en `storage/` y `bootstrap/cache/`. El código y `.env` deben quedar fuera de sus permisos de escritura.
- En producción configura `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<dominio>`, `AITROL_REQUIRE_HTTPS=true`, `SESSION_SECURE_COOKIE=true` y `SESSION_ENCRYPT=true`. Puedes definir `AITROL_TRUSTED_HOSTS` con los nombres exactos y `AITROL_TRUSTED_PROXIES` con las IP o redes de los proxies que sustituyen las cabeceras reenviadas. Regenera la caché de configuración y verifica el acceso HTTPS antes de habilitar el servicio.
- Usa una caché compartida entre las instancias de la aplicación para que el límite de intentos se aplique en todos los nodos.
- Guarda copias cifradas de la base de datos y archivos privados fuera del servidor, con retención inmutable y credenciales distintas de las de Aitrol. Conserva la clave de aplicación en un respaldo seguro y verifica que puedes restaurar datos y archivos en una instancia aislada.
- Ejecuta PHP con una cuenta de servicio sin privilegios administrativos y concede al usuario de base de datos solo los permisos que necesita la aplicación. Separa la cuenta usada para administrar el esquema. Mantén actualizado el sistema operativo, PHP y las dependencias.

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
