# Registro de Deuda Técnica — TP Integrador (Unidades 1 y 2)

**Metodología de Sistemas II — UTN FRRe (sede Formosa)**  
**Proyecto:** `laboratorio-pedidos`  
**Integrantes:** Grupo de Trabajo — MrGinesFG y equipo  

---

## Tabla de Deuda Técnica y Patrones Aplicados

| Síntoma observado | Evidencia (archivo:línea) | Tipo de deuda | Patrón aplicado | Consecuencia asumida |
|---|---|---|---|---|
| El descuento está duplicado en múltiples archivos y se usa un switch rígido para calcular precios | `src/Pricing/PriceCalculator.php:14`, `src/Models/Order.php:112` | Diseño (OCP + DRY) | **Strategy** | Mayor número de clases pequeñas para encapsular cada estrategia de cálculo. |
| Múltiples sentencias condicionales `if/else` repetidas para instanciar notificaciones | `src/Notifications/NotificationSender.php:18` | Diseño (OCP + DIP) | **Factory Method** | Centralización de la creación; agregar canales requiere registrar la nueva clase en la fábrica. |
| Avisos encadenados manualmente acoplados a implementaciones concretas | `src/Events/OrderEvents.php:15` | Diseño (DIP) | **Observer** | El orden de notificación a los observadores suscritos es no determinístico en el sujeto. |
| Servicio monolítico con múltiples responsabilidades y orquestación acoplada | `src/Services/OrderService.php:12` | Diseño (SRP) | **Facade** | Riesgo de que la fachada se convierta en una clase dios si acumula lógica en vez de coordinar. |
| Conexión nueva creada en cada consulta (`new Connection()`) y excepciones silenciadas | `src/Database/Connection.php:36`, `src/Database/Connection.php:67-68` | Diseño (Gestión de recursos) | **Singleton** | Introduce estado global que complejiza tests unitarios con dobles de prueba y oculta dependencias. |
| Clase de terceros modificada internamente y duplicación de lógica con copia y pega | `src/Legacy/LegacyNotifier.php:38`, `src/Legacy/LegacyNotifier.php:49-53` | Diseño (Límites del sistema) | **Adapter** | Introduce un nivel adicional de indirección y una clase adaptadora intermedia. |
| Método que recibe múltiples banderas booleanas combinadas (*boolean trap*) | `src/Reports/ReportGenerator.php:28-32` | Diseño (OCP) | **Decorator** | El orden de anidamiento de los decoradores altera el resultado final; requiere documentar convenciones. |
| Modelo `Order` acoplado con responsabilidades de persistencia directa y SQL | `src/Models/Order.php:45` | Diseño (SRP) | **Repository** | Se añade una capa extra de abstracción entre el modelo de dominio y la base de datos. |
| Controlador que mezcla consultas SQL, lógica de negocio y salida HTML directa | `src/Controllers/OrderController.php:15` | Diseño (MVC + SRP) | **MVC (Controlador)** | Desacoplamiento que requiere estructurar vistas y delegar en servicios/fachadas dedicadas. |
| Vista que ejecutaba consultas a BD, realizaba cálculos de negocio y no sanitizaba salida HTML | `views/orders.php:12` | Diseño (MVC + Seguridad) | **MVC (Vista)** | La vista pasa a ser pasiva y depende estrictamente de las variables pasadas por el controlador. |
| Requires manuales en cascada, credenciales hardcodeadas y ruteo estructurado con `if` | `public/index.php:10-25`, `public/index.php:71-74` | Diseño / Arquitectura | **Autoload + Config + Router** | Exige cumplimiento estricto de convención de nombres y estructura de carpetas `src/`. |
| Desarrollo directo sobre `main` sin validación previa ni trazabilidad colaborativa | Repositorio inicial sin branches ni PRs de revisión | Proceso (Unidad 1) | **Feature Branch + PR Workflow** | Mayor sobrecarga de tiempo de coordinación y revisión antes de cada integración. |
| Credenciales de base de datos expuestas en archivos versionados en Git | Constantes globales de DB en archivos de código fuente | Proceso (Unidad 1) | **External Config + `.gitignore`** | Cada desarrollador debe configurar su propio `config/database.php` local usando la plantilla. |

---

## Métricas del Refactor

- **Patrones de diseño aplicados:** 11 (Strategy, Factory, Observer, Facade, Singleton, Adapter, Decorator, Repository, MVC Controlador, MVC Vista, Autoload/Front Controller).
- **Reducción de dispersión del descuento (0.7):** Centralizado en estrategias dedicadas (`InsuranceStrategy`), eliminando su duplicación en 5 archivos distintos.
- **Flujo de trabajo:** 11 ramas `feat/*`, 11 Pull Requests creados y revisados con revisión cruzada entre integrantes.
