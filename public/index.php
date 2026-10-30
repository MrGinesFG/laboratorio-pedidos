<?php
/**
 * ============================================================================
 *  laboratorio-pedidos — PUNTO DE ENTRADA
 *  Metodologia de Sistemas II — UTN FRRe (sede Formosa)
 * ============================================================================
 *  Refactor aplicado:
 *   1. Autoload (spl_autoload_register): ya no hay un require por clase.
 *      Agregar una clase nueva en src/<Carpeta>/<Clase>.php no obliga a
 *      editar este archivo.
 *   2. Configuracion externa: las credenciales viven en config/database.php
 *      (ignorado por git). El repositorio versiona solo la plantilla
 *      config/database.example.php, con valores vacios.
 *   3. Tabla de rutas: un array accion => callable. Agregar una pantalla es
 *      agregar una linea al mapa, no modificar una cadena de if.
 * ============================================================================
 */

// 1. Autoload: busca <Clase>.php en cualquier subcarpeta de src/.
//    Requisito: una clase por archivo y el archivo se llama como la clase.
spl_autoload_register(function (string $class): void {
    $archivos = glob(__DIR__ . '/../src/*/' . $class . '.php') ?: [];
    if ($archivos !== []) {
        require_once $archivos[0];
    }
});

// 2. Configuracion externa (si no existe config/database.php, usa la plantilla).
$archivoConfig = is_file(__DIR__ . '/../config/database.php')
    ? __DIR__ . '/../config/database.php'
    : __DIR__ . '/../config/database.example.php';
$config = require $archivoConfig;

// Singleton: la configuracion se inyecta UNA sola vez, al arrancar.
Connection::getInstance($config);

// Raiz de composicion: aca (y solo aca) se arman las dependencias concretas.
$events = new OrderSubject();
$events->subscribe(new EmailObserver());
$events->subscribe(new SmsObserver());
$events->subscribe(new DashboardObserver());

$controller = new OrderController(new OrderFacade(
    new OrderValidator(),
    new NotificationSender(),
    $events,
    new OrderRepository(Connection::getInstance())
));

// 3. Tabla de rutas.
$rutas = [
    'crear'        => fn() => $controller->create(),
    'crear-facade' => fn() => $controller->create(),
    'listar'       => fn() => $controller->index(),
    'reporte'      => fn() => $controller->report(),
];

$accion = $_GET['accion'] ?? 'crear';

($rutas[$accion] ?? function (): void {
    http_response_code(404);
    echo 'Accion no encontrada';
})();
