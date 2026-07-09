# API REST de Productos — Laravel (imagen polimórfica uno a uno)

Examen práctico — Estándares y Métricas para el Desarrollo de Software — Universidad Politécnica de Pachuca.

API REST en Laravel 10 para administrar productos, donde cada producto tiene asociada una sola imagen
principal mediante una relación polimórfica uno a uno (`Product` `morphOne` `Image`, `Image` `morphTo`
`imageable`), pensada para que el modelo `Image` se pueda reutilizar en el futuro con otros módulos.

**La documentación completa del examen (plan de pruebas, casos de prueba, matriz de seguimiento, reporte de
defectos y conclusión técnica) está en [DOCUMENTACION.md](DOCUMENTACION.md).**

## Instalación rápida

```bash
composer install
cp .env.example .env
php artisan key:generate
type nul > database\database.sqlite   # (en Linux/Mac: touch database/database.sqlite)
php artisan migrate
php artisan storage:link
php artisan serve
```

La API queda disponible en `http://127.0.0.1:8000/api/products`.

## Endpoints

| Método | Ruta | Acción |
|---|---|---|
| GET | /api/products | Listar productos |
| GET | /api/products/{id} | Ver detalle de un producto |
| POST | /api/products | Crear producto (requiere `image`) |
| PUT/PATCH | /api/products/{id} | Actualizar producto (imagen opcional) |
| DELETE | /api/products/{id} | Eliminar producto |

## Evidencias de prueba

- `storage/app/test-assets/run-tests.ps1`: script que ejecuta todos los casos de prueba contra la API real.
- `storage/app/test-assets/evidencias.txt`: petición y respuesta real capturada de cada caso de prueba.
