<?php

/** Controlador y vistas (MVC). */
final class ViewTest extends ConnectionTestCase
{
    /** CP-VIS-01 */
    public function testElControladorRenderizaElReporteConTodosLosDecoradores(): void
    {
        $controller = new OrderController(new OrderFacade(
            new OrderValidator(),
            new NotificationSender(),
            new OrderSubject(),
            new OrderRepository(Connection::getInstance(self::CONFIG))
        ));

        ob_start();
        $controller->report();
        $salida = ob_get_clean();

        $this->assertStringContainsString(
            'Reporte: Pedidos del dia + firma digital + PDF + marca de agua',
            $salida
        );
    }

    /** CP-VIS-02 (negativo): la vista escapa la salida (prevencion de XSS). */
    public function testLaVistaEscapaElHtmlMalicioso(): void
    {
        $pedidos = [['id' => 1, 'paciente' => '<script>alert(1)</script>', 'total' => 100.0]];

        ob_start();
        require __DIR__ . '/../views/orders.php';
        $html = ob_get_clean();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}
