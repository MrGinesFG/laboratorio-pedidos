# Casos de prueba — laboratorio-pedidos

Cada caso esta automatizado con PHPUnit: el **ID** aparece en el comentario
`/** CP-XXX-NN */` del metodo de prueba correspondiente (carpeta `tests/`).

**Como ejecutarlos**

```
composer install
vendor/bin/phpunit          # en Windows: vendor\bin\phpunit
```

**Precondicion general (todos los casos):** dependencias instaladas con
`composer install`. Los casos de la seccion 7 (y los que usan `Connection`)
parten de una conexion recien reiniciada, porque `Connection` es un Singleton.

Tipo: **(+)** caso positivo, **(−)** caso negativo.

---

## 1. Calculo de precios — Strategy + Factory (`tests/PricingTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-PRC-01 | Obra social descuenta 30% | Ninguna | monto = 1000 | 1. Crear `InsuranceStrategy`. 2. Llamar `calculate(1000)` | 700 | (+) |
| CP-PRC-02 | Jubilado descuenta 50% | Ninguna | monto = 1000 | 1. Crear `RetireeStrategy`. 2. Llamar `calculate(1000)` | 500 | (+) |
| CP-PRC-03 | Prepaga descuenta 15% | Ninguna | monto = 1000 | 1. Crear `PrepaidStrategy`. 2. Llamar `calculate(1000)` | 850 | (+) |
| CP-PRC-04 | Particular paga completo | Ninguna | monto = 1000 | 1. Crear `PrivatePatientStrategy`. 2. Llamar `calculate(1000)` | 1000 | (+) |
| CP-PRC-05 | La Factory elige la estrategia segun el tipo | Ninguna | `obra_social`, `jubilado`, `prepaga` | 1. Llamar `PricingStrategyFactory::forPatientType(tipo)` para cada tipo | Devuelve `InsuranceStrategy`, `RetireeStrategy` y `PrepaidStrategy` respectivamente | (+) |
| CP-PRC-06 | Tipo de paciente inexistente no recibe descuento | Ninguna | tipo = `vip`, monto = 1000 | 1. Pedir la estrategia para `vip`. 2. Calcular 1000 | Devuelve `PrivatePatientStrategy` y el resultado es 1000 | (−) |
| CP-PRC-07 | El calculador delega en cualquier estrategia inyectada (OCP) | Estrategia falsa que siempre devuelve 42 | monto = 1000 | 1. Crear `PriceCalculator` con la estrategia falsa. 2. Llamar `calculate(1000)` | 42 | (+) |
| CP-PRC-08 | Calculo con IVA: descuento y luego 21% | Ninguna | obra social, monto = 1000 | 1. Crear `PriceCalculator(InsuranceStrategy)`. 2. Llamar `calculateWithTax(1000)` | 847 (700 × 1,21) | (+) |

## 2. Notificaciones — Factory + Adapter (`tests/NotificationTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-NOT-01 | Crear y enviar un email | Ninguna | tipo `email`, destino `ana@mail.com`, mensaje `hola` | 1. `NotificationFactory::create(...)`. 2. `send('hola')` | Salida: `[EMAIL] para ana@mail.com \| Pedido del laboratorio: hola<br>` | (+) |
| CP-NOT-02 | Crear y enviar un SMS | Ninguna | tipo `sms`, destino `3704000000` | 1. Crear con la Factory. 2. `send('hola')` | Salida: `[SMS] a 3704000000: hola<br>` | (+) |
| CP-NOT-03 | Crear y enviar un WhatsApp | Ninguna | tipo `whatsapp`, destino `3704000000` | 1. Crear con la Factory. 2. `send('hola')` | Salida: `[WHATSAPP] a 3704000000: hola<br>` | (+) |
| CP-NOT-04 | El tipo `legacy` devuelve el Adapter | Ninguna | tipo `legacy` | 1. Crear con la Factory. 2. `send('hola')` | Instancia de `LegacyNotifierAdapter`; salida `[LEGACY] hola<br>` | (+) |
| CP-NOT-05 | Tipo de notificacion no soportado | Ninguna | tipo `telegram` | 1. Llamar `NotificationFactory::create('telegram', 'x')` | Lanza `InvalidArgumentException`: `Tipo de notificacion no soportado: telegram` | (−) |
| CP-NOT-06 | `NotificationSender` delega en la Factory | Ninguna | `sms`, `3704000000`, `aviso` | 1. Llamar `enviar(...)` | Salida: `[SMS] a 3704000000: aviso<br>` | (+) |
| CP-NOT-07 | El Adapter traduce `send()` a `sendMessage()` | `LegacyNotifier` de prueba que registra los mensajes | mensaje `hola` | 1. Crear el Adapter con el legacy de prueba. 2. `send('hola')` | El legacy recibio `['hola']`; el Adapter cumple el contrato `Notification` | (+) |

## 3. Observer (`tests/ObserverTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-OBS-01 | `notify` avisa a todos los suscriptos | Dos observadores espia | Pedido id = 9 | 1. Suscribir los dos espias. 2. `notify(pedido)` | Ambos recibieron el id 9 | (+) |
| CP-OBS-02 | `notify` sin suscriptores | `OrderSubject` sin suscriptos | Pedido id = 1 | 1. `notify(pedido)` | No lanza error y no produce salida | (−) |
| CP-OBS-03 | `EmailObserver` envia el aviso | Ninguna | Pedido id = 5 | 1. `update(pedido)` | Salida: `[EMAIL] para paciente@mail.com \| Pedido del laboratorio: Pedido 5 creado<br>` | (+) |

## 4. Reportes — Decorator (`tests/ReportTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-REP-01 | Reporte basico sin decoradores | Ninguna | contenido `Pedidos del dia` | 1. `new BasicReport(...)`. 2. `generate()` | `Reporte: Pedidos del dia` | (+) |
| CP-REP-02 | Decoradores en el orden convenido | Ninguna | contenido `X` | 1. Envolver: firma → PDF → marca de agua. 2. `generate()` | `Reporte: X + firma digital + PDF + marca de agua` | (+) |
| CP-REP-03 | Invertir el orden cambia el resultado | Ninguna | contenido `X` | 1. Armar firma→PDF y PDF→firma. 2. Comparar `generate()` | Los dos textos son distintos | (−) |

## 5. Validacion de entrada (`tests/ValidationTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-VAL-01 | Datos validos | Ninguna | `Ana Gomez`, 100 | 1. `OrderValidator::validate(...)` | No lanza excepcion | (+) |
| CP-VAL-02 | Paciente vacio (solo espacios) | Ninguna | `'   '`, 100 | 1. `validate(...)` | `InvalidArgumentException`: `Falta el paciente` | (−) |
| CP-VAL-03 | Monto cero | Ninguna | `Ana Gomez`, 0 | 1. `validate(...)` | `InvalidArgumentException`: `Monto invalido` | (−) |
| CP-VAL-04 | Monto negativo | Ninguna | `Ana Gomez`, -50 | 1. `validate(...)` | `InvalidArgumentException`: `Monto invalido` | (−) |
| CP-ORD-01 | `Order` valido expone sus datos | Ninguna | 1, `Juan Perez`, 15000, `obra_social` | 1. Crear `Order`. 2. Leer sus propiedades | Las propiedades coinciden con lo ingresado | (+) |
| CP-ORD-02 | `Order` con importe negativo | Ninguna | importe = -1 | 1. Crear `Order` | `InvalidArgumentException`: `El importe no puede ser negativo` | (−) |
| CP-ORD-03 | `Order` sin paciente | Ninguna | paciente = `''` | 1. Crear `Order` | `InvalidArgumentException`: `El paciente es obligatorio` | (−) |
| CP-ORD-04 | `Order` es inmutable | `Order` creado | `$order->id = 99` | 1. Intentar asignar una propiedad `readonly` | Lanza `Error` | (−) |

## 6. Conexion (Singleton) y Repositorio (`tests/DatabaseTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-DB-01 | Una sola conexion | Conexion reiniciada | config vacia | 1. `getInstance(config)`. 2. `getInstance()` | Ambas llamadas devuelven el mismo objeto | (+) |
| CP-DB-02 | Conexion sin configurar | Conexion reiniciada | sin config | 1. `getInstance()` | Lanza `LogicException` | (−) |
| CP-DB-03 | SQL vacio | Conexion configurada | `'   '` | 1. `ejecutar('   ')` | Lanza `InvalidArgumentException` (el error ya no se silencia) | (−) |
| CP-DB-04 | Registro de consultas | Conexion configurada | `SELECT 1`, `['a' => 1]` | 1. `ejecutar(...)`. 2. `consultas()` | Una entrada con el SQL y sus parametros | (+) |
| CP-DB-05 | Guardar usa parametros con nombre | Conexion configurada | `Order(7, 'Ana Gomez', 500, 'particular')` | 1. `OrderRepository::save(order)`. 2. Leer la consulta registrada | El SQL contiene `:patient`; los valores van en `params` | (+) |
| CP-DB-06 | Intento de inyeccion SQL | Conexion configurada | paciente = `x'); DROP TABLE orders;--` | 1. `save(order)`. 2. Leer la consulta registrada | El SQL no contiene `DROP`; el texto viaja solo como parametro | (−) |

## 7. Fachada de pedidos (`tests/OrderFacadeTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-FAC-01 | Crear pedido: flujo completo | Facade armada con un observador espia | id 1, `Juan Perez`, 1000, `obra_social`, `email`, `paciente@mail.com` | 1. `createOrder(...)` | Total = 700; 1 consulta guardada; 1 notificacion por email; el espia recibio el pedido | (+) |
| CP-FAC-02 | Pedido sin paciente | Igual que CP-FAC-01 | paciente = `'   '` | 1. `createOrder(...)` | `InvalidArgumentException` (`Falta el paciente`); no se guarda ni se notifica nada | (−) |
| CP-FAC-03 | Pedido con monto invalido | Igual que CP-FAC-01 | monto = 0 | 1. `createOrder(...)` | `InvalidArgumentException` (`Monto invalido`); no se guarda nada | (−) |
| CP-FAC-04 | Listar pedidos con total calculado | Igual que CP-FAC-01 | — | 1. `listOrders()` | 2 pedidos; totales 10500 (obra social) y 22000 (particular) | (+) |

## 8. Controlador y vistas — MVC (`tests/ViewTest.php`)

| ID | Descripcion | Precondiciones | Datos de entrada | Pasos | Resultado esperado | Tipo |
|---|---|---|---|---|---|---|
| CP-VIS-01 | El controlador renderiza el reporte decorado | Controlador armado con la Facade | — | 1. `report()` capturando la salida | Contiene `Reporte: Pedidos del dia + firma digital + PDF + marca de agua` | (+) |
| CP-VIS-02 | La vista escapa HTML malicioso | Ninguna | paciente = `<script>alert(1)</script>` | 1. Renderizar `views/orders.php` | La salida no contiene `<script>`; contiene `&lt;script&gt;` | (−) |

---

**Resumen:** 41 casos (25 positivos y 16 negativos) sobre 8 funcionalidades, todas con al menos un caso positivo y uno negativo.
