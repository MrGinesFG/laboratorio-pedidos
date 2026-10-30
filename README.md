# laboratorio-pedidos — Proyecto Integrador de Patrones de Diseño

Sistema de gestión de pedidos de un laboratorio de análisis clínicos.  
**PHP Vanilla, sin frameworks, sin Composer.** Corre en XAMPP tal cual está.

> ✅ **Proyecto completado con 11 patrones de diseño aplicados.**  
> Cada módulo con deuda técnica sembrada fue diagnosticado con evidencia y refactorizado en ramas independientes mediante Pull Requests colaborativos y revisión cruzada.

---

## Cómo levantarlo en local (XAMPP)

1. Copiar o clonar la carpeta del proyecto dentro de `C:\xampp\htdocs\laboratorio-pedidos` (o correr el servidor embebido de PHP con `php -S localhost:8000 -t public`).
2. *(Opcional)* Si se va a conectar a MySQL, copiar `config/database.example.php` a `config/database.php` con las credenciales locales. Si no existe, el sistema utiliza la persistencia simulada en memoria.
3. Abrir en el navegador:
   - `http://localhost/laboratorio-pedidos/public/index.php`

### Acciones disponibles:

| URL | Qué hace |
|---|---|
| `public/index.php?accion=crear` | Crea un pedido ejecutando la fachada, validación, cálculo de precio y notificación a observadores |
| `public/index.php?accion=crear-facade` | Misma acción unificada mediante `OrderFacade` |
| `public/index.php?accion=listar` | Lista los pedidos persistidos a través de la vista limpia MVC |
| `public/index.php?accion=reporte` | Genera el reporte del día componiendo decoradores (firma digital, PDF, marca de agua) |

---

## Mapa de los 11 patrones aplicados

| # | Componente / Archivo | Problema / Deuda resuelta | Patrón aplicado | Tipo | PR |
|---|---|---|---|---|---|
| 1 | `src/Pricing/PriceCalculator.php` | Switch gigante y lógica duplicada para descuentos | **Strategy** | Comportamiento | PR #1 |
| 2 | `src/Events/OrderEvents.php` | Avisos acoplados directamente a clases concretas | **Observer** | Comportamiento | PR #2 |
| 3 | `src/Notifications/NotificationSender.php` | Condicionales repetidos para instanciar notificaciones | **Factory Method** | Creacional | PR #3 / #4 |
| 4 | `src/Services/OrderService.php` | Método que concentraba todas las responsabilidades | **Facade** | Estructural | PR #4 |
| 5 | `src/Database/Connection.php` | Conexión nueva por consulta (`new`) y error silenciado | **Singleton** | Creacional | PR #5 |
| 6 | `src/Legacy/LegacyNotifier.php` | Código de terceros alterado y duplicación de clases | **Adapter** | Estructural | PR #6 |
| 7 | `src/Reports/ReportGenerator.php` | Parámetros booleanos combinados (*boolean trap*) | **Decorator** | Estructural | PR #7 |
| 8 | `src/Models/Order.php` | Modelo que calculaba precios y persistía en BD | **Repository** | Arquitectura | PR #8 |
| 9 | `src/Controllers/OrderController.php` | Controlador mezclado con SQL, lógica y HTML | **MVC (Controlador)** | Arquitectura | PR #9 |
| 10 | `views/orders.php` | Vista con consultas SQL, lógica y sin sanitización | **MVC (Vista)** | Arquitectura | PR #10 |
| 11 | `public/index.php` | Requires manuales, credenciales y ruteo con `if` | **Autoload + Router** | Arquitectura | PR #11 |

---

## Métrica del proyecto

En el estado inicial, el descuento de obra social (`0.7`) estaba disperso y duplicado en **cinco archivos distintos**:
```
src/Models/Order.php
src/Pricing/PriceCalculator.php (duplicado)
src/Services/OrderService.php
src/Controllers/OrderController.php
views/orders.php
```

Con la aplicación de **Strategy** y **Repository**, dicho valor quedó centralizado en **un único archivo** (`InsuranceStrategy.php`). Para modificar o agregar un convenio nuevo, ahora se añade una única clase sin tocar el resto del sistema (principio Abierto/Cerrado).

---

## Flujo de trabajo y colaboración

- **Metodología:** Feature Branch Workflow (`feat/patron-*`).
- **Revisiones cruzadas:** Cada Pull Request contó con descripción técnica (deuda con línea, patrón aplicado, justificación y consecuencias negativas asumidas) y fue revisado y aprobado por otro compañero de equipo antes de integrarse a `main`.
- **Registro completo de deuda:** Detallado en [`docs/DEUDA-TECNICA.md`](docs/DEUDA-TECNICA.md).
