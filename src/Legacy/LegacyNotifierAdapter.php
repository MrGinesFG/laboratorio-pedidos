<?php
/**
 * ============================================================================
 *  ADAPTER del sistema de avisos heredado (estructural)
 * ============================================================================
 *  Nuestro sistema -> Notification <- LegacyNotifierAdapter -> LegacyNotifier
 *
 *  El adapter cumple NUESTRO contrato (Notification::send) y traduce la
 *  llamada al metodo del proveedor (sendMessage). Usa COMPOSICION: tiene un
 *  LegacyNotifier adentro, no lo hereda ni lo modifica.
 *
 *  Si manana el proveedor renombra sendMessage() a dispatch(), cambia UN solo
 *  archivo: este.
 * ============================================================================
 */

final class LegacyNotifierAdapter implements Notification
{
    public function __construct(private LegacyNotifier $legacy)
    {
    }

    public function send(string $message): void
    {
        $this->legacy->sendMessage($message);
    }
}
