<?php
/**
 * Vista: pedido creado. Solo presenta datos ya resueltos y escapa la salida.
 * Variables esperadas: $order (Order).
 */
?>
<h1>Pedido creado</h1>
<p>Paciente: <?= htmlspecialchars($order->patient, ENT_QUOTES, 'UTF-8') ?></p>
<p>Total: $ <?= htmlspecialchars((string) $order->amount, ENT_QUOTES, 'UTF-8') ?></p>
