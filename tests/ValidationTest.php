<?php

use PHPUnit\Framework\TestCase;

/** Reglas de entrada: OrderValidator y el modelo Order. */
final class ValidationTest extends TestCase
{
    /** CP-VAL-01 */
    public function testDatosValidosNoLanzanExcepcion(): void
    {
        $this->expectNotToPerformAssertions();

        (new OrderValidator())->validate('Ana Gomez', 100.0);
    }

    /** CP-VAL-02 (negativo) */
    public function testPacienteVacioONumeroDeEspaciosEsRechazado(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Falta el paciente');

        (new OrderValidator())->validate('   ', 100.0);
    }

    /** CP-VAL-03 (negativo) */
    public function testMontoCeroEsRechazado(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Monto invalido');

        (new OrderValidator())->validate('Ana Gomez', 0.0);
    }

    /** CP-VAL-04 (negativo) */
    public function testMontoNegativoEsRechazado(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Monto invalido');

        (new OrderValidator())->validate('Ana Gomez', -50.0);
    }

    /** CP-ORD-01 */
    public function testOrderValidoExponeSusDatos(): void
    {
        $order = new Order(1, 'Juan Perez', 15000.0, 'obra_social');

        $this->assertSame(1, $order->id);
        $this->assertSame('Juan Perez', $order->patient);
        $this->assertSame(15000.0, $order->amount);
        $this->assertSame('obra_social', $order->patientType);
    }

    /** CP-ORD-02 (negativo) */
    public function testOrderConImporteNegativoNoPuedeCrearse(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El importe no puede ser negativo');

        new Order(1, 'Juan Perez', -1.0, 'particular');
    }

    /** CP-ORD-03 (negativo) */
    public function testOrderSinPacienteNoPuedeCrearse(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El paciente es obligatorio');

        new Order(1, '', 100.0, 'particular');
    }

    /** CP-ORD-04 (negativo): las propiedades son readonly. */
    public function testOrderEsInmutable(): void
    {
        $order = new Order(1, 'Juan Perez', 100.0, 'particular');

        $this->expectException(Error::class);

        $order->id = 99;
    }
}
