<?php

use PHPUnit\Framework\TestCase;

/** Factory de notificaciones + Adapter del sistema heredado. */
final class NotificationTest extends TestCase
{
    /** CP-NOT-01 */
    public function testFactoryCreaYEnviaEmail(): void
    {
        $this->expectOutputString('[EMAIL] para ana@mail.com | Pedido del laboratorio: hola<br>');

        NotificationFactory::create('email', 'ana@mail.com')->send('hola');
    }

    /** CP-NOT-02 */
    public function testFactoryCreaYEnviaSms(): void
    {
        $this->expectOutputString('[SMS] a 3704000000: hola<br>');

        NotificationFactory::create('sms', '3704000000')->send('hola');
    }

    /** CP-NOT-03 */
    public function testFactoryCreaYEnviaWhatsApp(): void
    {
        $this->expectOutputString('[WHATSAPP] a 3704000000: hola<br>');

        NotificationFactory::create('whatsapp', '3704000000')->send('hola');
    }

    /** CP-NOT-04 */
    public function testFactoryCreaElAdapterDelSistemaHeredado(): void
    {
        $this->expectOutputString('[LEGACY] hola<br>');

        $notificacion = NotificationFactory::create('legacy', '');

        $this->assertInstanceOf(LegacyNotifierAdapter::class, $notificacion);
        $notificacion->send('hola');
    }

    /** CP-NOT-05 (negativo) */
    public function testTipoDeNotificacionNoSoportadoLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de notificacion no soportado: telegram');

        NotificationFactory::create('telegram', 'x');
    }

    /** CP-NOT-06 */
    public function testNotificationSenderDelegaEnLaFactory(): void
    {
        $this->expectOutputString('[SMS] a 3704000000: aviso<br>');

        (new NotificationSender())->enviar('sms', '3704000000', 'aviso');
    }

    /** CP-NOT-07: el Adapter traduce send() al sendMessage() del proveedor. */
    public function testElAdapterTraduceSendASendMessage(): void
    {
        $legacy = new class extends LegacyNotifier {
            public array $mensajes = [];

            public function sendMessage(string $text): void
            {
                $this->mensajes[] = $text;
            }
        };

        $adapter = new LegacyNotifierAdapter($legacy);
        $adapter->send('hola');

        $this->assertInstanceOf(Notification::class, $adapter);
        $this->assertSame(['hola'], $legacy->mensajes);
    }
}
