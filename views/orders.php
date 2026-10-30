<?php
/**
 * ============================================================================
 *  VISTA: listado de pedidos
 * ============================================================================
 *  Antes: la vista abria la conexion, tenia los datos escritos adentro,
 *  calculaba el descuento de obra social e imprimia sin escapar (XSS).
 *
 *  Ahora la vista SOLO muestra: recibe $pedidos ya resuelto desde el
 *  controlador (cada item con id, paciente y total ya calculado) y escapa
 *  toda la salida.
 * ============================================================================
 */
?>
<h1>Pedidos</h1>

<table border="1">
    <tr><th>ID</th><th>Paciente</th><th>Total</th></tr>

    <?php foreach ($pedidos as $pedido): ?>
        <tr>
            <td><?= htmlspecialchars((string) $pedido['id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($pedido['paciente'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $pedido['total'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
