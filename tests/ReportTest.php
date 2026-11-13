<?php

use PHPUnit\Framework\TestCase;

/** Decorator de reportes. */
final class ReportTest extends TestCase
{
    /** CP-REP-01 */
    public function testReporteBasicoSinDecoradores(): void
    {
        $this->assertSame('Reporte: Pedidos del dia', (new BasicReport('Pedidos del dia'))->generate());
    }

    /** CP-REP-02: orden convenido por el equipo: firma -> PDF -> marca de agua. */
    public function testLosDecoradoresSeAcumulanEnElOrdenConvenido(): void
    {
        $reporte = new WatermarkDecorator(
            new PdfReportDecorator(
                new DigitalSignatureDecorator(new BasicReport('X'))
            )
        );

        $this->assertSame('Reporte: X + firma digital + PDF + marca de agua', $reporte->generate());
    }

    /** CP-REP-03 (negativo): invertir el orden cambia el resultado. */
    public function testInvertirElOrdenDeLosDecoradoresCambiaElResultado(): void
    {
        $convenido = new PdfReportDecorator(new DigitalSignatureDecorator(new BasicReport('X')));
        $invertido = new DigitalSignatureDecorator(new PdfReportDecorator(new BasicReport('X')));

        $this->assertNotSame($convenido->generate(), $invertido->generate());
    }
}
