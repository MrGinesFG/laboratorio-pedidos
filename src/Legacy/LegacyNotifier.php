<?php
/**
 * ============================================================================
 *  SISTEMA HEREDADO DEL LABORATORIO
 * ============================================================================
 *  CONTEXTO: el laboratorio ya tenia un sistema de avisos hecho por otro
 *  proveedor. No lo podemos modificar: lo usan tambien Recepcion y
 *  Facturacion.
 *
 *  Esta clase queda EXACTAMENTE como la entrega el proveedor: solo
 *  sendMessage(). Antes habia un send() agregado por nosotros adentro, y una
 *  copia del comportamiento en otra clase. Ambos se eliminaron: la
 *  adaptacion vive afuera, en LegacyNotifierAdapter.
 * ============================================================================
 */

class LegacyNotifier
{
    /** Metodo original del proveedor. No lo podemos cambiar. */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}
