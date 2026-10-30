<?php
/**
 * Vista: error de entrada. Variables esperadas: $mensaje (string).
 */
?>
<h1>No se pudo crear el pedido</h1>
<p><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
