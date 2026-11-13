<?php

/** Facade: orquesta validar -> calcular -> guardar -> notificar -> avisar. */
final class OrderFacadeTest extends ConnectionTestCase
{
    private OrderFacade $facade;
    private Connection $conexion;
    private object $espia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conexion = Connection::getInstance(self::CONFIG);

        $this->espia = new class implements OrderObserver {
            public array $recibidos = [];

            public function update(Order $order): void
            {
                $this->recibidos[] = $order;
            }
        };

        $eventos = new OrderSubject();
        $eventos->subscribe($this->espia);

        $this->facade = new OrderFacade(
            new OrderValidator(),
            new NotificationSender(),
            $eventos,
            new OrderRepository($this->conexion)
        );
    }

    /** CP-FAC-01 */
    public function testCrearPedidoRecorreTodoElFlujo(): void
    {
        $this->expectOutputRegex('/\[EMAIL\] para paciente@mail\.com/');

        $order = $this->facade->createOrder(1, 'Juan Perez', 1000.0, 'obra_social', 'email', 'paciente@mail.com');

        $this->assertEqualsWithDelta(700.0, $order->amount, 0.0001);
        $this->assertCount(1, $this->conexion->consultas());
        $this->assertCount(1, $this->espia->recibidos);
        $this->assertSame($order, $this->espia->recibidos[0]);
    }

    /** CP-FAC-02 (negativo): si la validacion falla no se guarda ni se notifica nada. */
    public function testPedidoSinPacienteNoSeGuardaNiSeNotifica(): void
    {
        $this->expectOutputString('');

        try {
            $this->facade->createOrder(1, '   ', 1000.0, 'obra_social', 'email', 'x@mail.com');
            $this->fail('Debia lanzar InvalidArgumentException');
        } catch (InvalidArgumentException $e) {
            $this->assertSame('Falta el paciente', $e->getMessage());
        }

        $this->assertCount(0, $this->conexion->consultas());
        $this->assertCount(0, $this->espia->recibidos);
    }

    /** CP-FAC-03 (negativo) */
    public function testPedidoConMontoInvalidoNoSeGuarda(): void
    {
        $this->expectOutputString('');

        try {
            $this->facade->createOrder(1, 'Ana Gomez', 0.0, 'particular', 'sms', '3704000000');
            $this->fail('Debia lanzar InvalidArgumentException');
        } catch (InvalidArgumentException $e) {
            $this->assertSame('Monto invalido', $e->getMessage());
        }

        $this->assertCount(0, $this->conexion->consultas());
    }

    /** CP-FAC-04 */
    public function testListarPedidosDevuelveElTotalYaCalculado(): void
    {
        $pedidos = $this->facade->listOrders();

        $this->assertCount(2, $pedidos);
        $this->assertSame('Juan Perez', $pedidos[0]['paciente']);
        $this->assertEqualsWithDelta(10500.0, $pedidos[0]['total'], 0.0001);
        $this->assertEqualsWithDelta(22000.0, $pedidos[1]['total'], 0.0001);
    }
}
