<?php
/**
 * ============================================================================
 *  REPORTES — DECORATOR (estructural)
 * ============================================================================
 *  Antes: ReportGenerator::generate($contenido, true, false, true) recibia
 *  banderas booleanas (boolean trap): con 3 banderas hay 8 combinaciones
 *  dentro del mismo metodo, y nadie recuerda que significa cada `true`.
 *
 *  Ahora: un contrato (Report), un reporte base (BasicReport) y decoradores
 *  que lo envuelven. Cada agregado es una clase que se combina en tiempo de
 *  ejecucion; sumar un agregado nuevo (ej. codigo QR) es una clase nueva,
 *  sin tocar ninguna de las existentes.
 *
 *  ATENCION — EL ORDEN IMPORTA: los decoradores se aplican de adentro hacia
 *  afuera y cada uno agrega su texto sobre el resultado del anterior.
 *  Firmar y despues pasar a PDF NO es lo mismo que pasar a PDF y despues
 *  firmar. Convencion del equipo: firma -> PDF -> marca de agua.
 * ============================================================================
 */

interface Report
{
    public function generate(): string;
}
