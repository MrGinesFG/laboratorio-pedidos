# Mapa de deudas — laboratorio-pedidos

Planilla de trabajo para la clase práctica. Cada grupo completa **una fila**
durante la ronda de refactor y la usa como descripción de su Pull Request.

| Archivo | Síntoma sembrado | Principio violado | Patrón que lo repara | Grupo | Rama | PR |
|---|---|---|---|---|---|---|
| `src/Pricing/PriceCalculator.php` | `switch` con todos los algoritmos, duplicado | OCP + DRY | Strategy | Grupo 1 | `feat/patron-strategy` | #1 |
| `src/Events/OrderEvents.php` | Avisos encadenados a clases concretas | DIP | Observer | Grupo 1 | `feat/patron-observer` | #2 |
| `src/Notifications/NotificationSender.php` | `if` por tipo repetido en 3 archivos | OCP + DIP | Factory | Grupo 1 | `feat/patron-factory` | #3 / #4 |
| `src/Services/OrderService.php` | Método que hace de todo | SRP | Facade | Grupo 1 | `feat/patron-facade` | #4 |
| `src/Database/Connection.php` | Conexión nueva por consulta, excepción silenciada | Gestión de recursos | Singleton | Grupo 1 | `feat/patron-singleton` | #5 |
| `src/Legacy/LegacyNotifier.php` | Clase de terceros modificada + copia y pega | DRY + límites del sistema | Adapter | Grupo 1 | `feat/patron-adapter` | #6 |
| `src/Reports/ReportGenerator.php` | Banderas booleanas (`boolean trap`) | OCP | Decorator | Grupo 1 | `feat/patron-decorator` | #7 |
| `src/Models/Order.php` | El modelo persiste y calcula precios | SRP | Repository + Strategy | Grupo 1 | `feat/patron-repository` | #8 |
| `src/Controllers/OrderController.php` | SQL + negocio + HTML juntos | SRP + MVC | MVC Controlador | Grupo 1 | `feat/patron-mvc-controller` | #9 |
| `views/orders.php` | Consulta, calcula y no escapa | MVC + seguridad | MVC Vista | Grupo 1 | `feat/patron-mvc-vista` | #10 |
| `public/index.php` | Requires manuales, credenciales, ruteo con `if` | OCP + configuración en código | Autoload + tabla de rutas | Grupo 1 | `feat/patron-autoload` | #11 |

## Referencias

- **SRP** — una clase, un motivo de cambio.
- **OCP** — abierto a extensión, cerrado a modificación.
- **DIP** — depender de abstracciones, no de implementaciones concretas.
- **DRY** — el mismo conocimiento no se escribe dos veces.

## Métrica del proyecto

El descuento de obra social (`0.7`) estaba escrito en **5 archivos** en el código inicial.
Tras aplicar los 11 patrones de diseño, quedó centralizado en **1 solo lugar** (`InsuranceStrategy.php`), eliminando la duplicación en todo el sistema.
