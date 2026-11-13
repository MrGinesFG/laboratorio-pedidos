<?php

use PHPUnit\Framework\TestCase;

/** Observer: OrderSubject y los observadores concretos. */
final class ObserverTest extends TestCase
{
    private function crearEspia(): OrderObserver
    {
        return new class implements OrderObserver {
            public array $ids = [];

            public function update(Order $order): void
            {
                $this->ids[] = $order->id;
            }
        };
    }

    /** CP-OBS-01 */
    public function testNotifyAvisaATodosLosSuscriptos(): void
    {
        $primero = $this->crearEspia();
        $segundo = $this->crearEspia();

        $sujeto = new OrderSubject();
        $sujeto->subscribe($primero);
        $sujeto->subscribe($segundo);
        $sujeto->notify(new Order(9, 'Ana Gomez', 100.0, 'particular'));

        $this->assertSame([9], $primero->ids);
        $this->assertSame([9], $segundo->ids);
    }

    /** CP-OBS-02 (negativo) */
    public function testNotifySinSuscriptoresNoFallaNiEmiteSalida(): void
    {
        $this->expectOutputString('');

        (new OrderSubject())->notify(new Order(1, 'Ana Gomez', 100.0, 'particular'));
    }

    /** CP-OBS-03 */
    public function testEmailObserverEnviaElAvisoDelPedido(): void
    {
        $this->expectOutputString('[EMAIL] para paciente@mail.com | Pedido del laboratorio: Pedido 5 creado<br>');

        (new EmailObserver())->update(new Order(5, 'Ana Gomez', 100.0, 'particular'));
    }
}
