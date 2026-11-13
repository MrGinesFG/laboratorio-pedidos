<?php

use PHPUnit\Framework\TestCase;

/** Strategy (calculo de precios) + su Factory. */
final class PricingTest extends TestCase
{
    private const DELTA = 0.0001;

    /** CP-PRC-01 */
    public function testObraSocialAplicaDescuentoDel30PorCiento(): void
    {
        $this->assertEqualsWithDelta(700.0, (new InsuranceStrategy())->calculate(1000.0), self::DELTA);
    }

    /** CP-PRC-02 */
    public function testJubiladoAplicaDescuentoDel50PorCiento(): void
    {
        $this->assertEqualsWithDelta(500.0, (new RetireeStrategy())->calculate(1000.0), self::DELTA);
    }

    /** CP-PRC-03 */
    public function testPrepagaAplicaDescuentoDel15PorCiento(): void
    {
        $this->assertEqualsWithDelta(850.0, (new PrepaidStrategy())->calculate(1000.0), self::DELTA);
    }

    /** CP-PRC-04 */
    public function testParticularPagaElMontoCompleto(): void
    {
        $this->assertEqualsWithDelta(1000.0, (new PrivatePatientStrategy())->calculate(1000.0), self::DELTA);
    }

    /** CP-PRC-05 */
    public function testFactoryDevuelveLaEstrategiaCorrectaParaCadaTipo(): void
    {
        $this->assertInstanceOf(InsuranceStrategy::class, PricingStrategyFactory::forPatientType('obra_social'));
        $this->assertInstanceOf(RetireeStrategy::class, PricingStrategyFactory::forPatientType('jubilado'));
        $this->assertInstanceOf(PrepaidStrategy::class, PricingStrategyFactory::forPatientType('prepaga'));
    }

    /** CP-PRC-06 (negativo) */
    public function testTipoDePacienteDesconocidoNoRecibeDescuento(): void
    {
        $strategy = PricingStrategyFactory::forPatientType('vip');

        $this->assertInstanceOf(PrivatePatientStrategy::class, $strategy);
        $this->assertEqualsWithDelta(1000.0, $strategy->calculate(1000.0), self::DELTA);
    }

    /** CP-PRC-07: demuestra el OCP, el calculador acepta cualquier estrategia nueva. */
    public function testElCalculadorDelegaEnLaEstrategiaInyectada(): void
    {
        $fija = new class implements PricingStrategy {
            public function calculate(float $amount): float
            {
                return 42.0;
            }
        };

        $this->assertSame(42.0, (new PriceCalculator($fija))->calculate(1000.0));
    }

    /** CP-PRC-08 */
    public function testCalcularConIvaAplicaDescuentoYLuegoElIva(): void
    {
        $calculador = new PriceCalculator(new InsuranceStrategy());

        $this->assertEqualsWithDelta(847.0, $calculador->calculateWithTax(1000.0), self::DELTA);
    }
}
