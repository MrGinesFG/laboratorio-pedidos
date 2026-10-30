<?php
/**
 * ============================================================================
 *  CONTROLADOR DE PEDIDOS — MVC + SRP
 * ============================================================================
 *  Antes (God Controller): armaba SQL, decidia el descuento, imprimia HTML con
 *  echo, creaba con `new` todas sus dependencias y repetia el if de
 *  notificaciones.
 *
 *  Ahora el controlador solo:
 *   1. Recibe la entrada del request.
 *   2. Llama a UN servicio (OrderFacade), que ya sabe validar, calcular,
 *      guardar, notificar y avisar.
 *   3. Elige que vista renderizar.
 *
 *  Sus dependencias llegan por constructor (las arma index.php).
 * ============================================================================
 */

final class OrderController
{
    public function __construct(private OrderFacade $facade)
    {
    }

    /** Atiende las acciones `crear` y `crear-facade` (ahora son el mismo caso de uso). */
    public function create(): void
    {
        try {
            $order = $this->facade->createOrder(...$this->pedidoDesdeRequest());
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            $this->render('error', ['mensaje' => $e->getMessage()]);
            return;
        }
        $this->render('order_created', ['order' => $order]);
    }

    public function index(): void
    {
        $this->render('orders', ['pedidos' => $this->facade->listOrders()]);
    }

    public function report(): void
    {
        $reporte = new BasicReport('Pedidos del dia');
        $reporte = new DigitalSignatureDecorator($reporte);
        $reporte = new PdfReportDecorator($reporte);
        $reporte = new WatermarkDecorator($reporte);

        $this->render('report', ['contenido' => $reporte->generate()]);
    }

    /** Lee y castea la entrada. La validacion de negocio la hace el Facade. */
    private function pedidoDesdeRequest(): array
    {
        return [
            (int) ($_GET['id'] ?? 1),
            (string) ($_GET['paciente'] ?? 'Juan Perez'),
            (float) ($_GET['monto'] ?? 15000),
            (string) ($_GET['tipo'] ?? 'obra_social'),
            (string) ($_GET['canal'] ?? 'email'),
            (string) ($_GET['destino'] ?? 'paciente@mail.com'),
        ];
    }

    private function render(string $vista, array $datos = []): void
    {
        extract($datos, EXTR_SKIP);
        require __DIR__ . '/../../views/' . $vista . '.php';
    }
}
