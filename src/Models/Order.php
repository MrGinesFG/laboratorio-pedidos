<?php
/**
 * ============================================================================
 *  MODELO Order
 * ============================================================================
 *  Antes: propiedades publicas mutables, sin validacion, y el modelo se
 *  persistia a si mismo (guardar(), con SQL concatenado) y calculaba precios
 *  por tipo de paciente (calcularTotal(), con un if por tipo).
 *
 *  Ahora Order es solo dominio:
 *   1. Propiedades readonly + validacion en el constructor: no puede nacer
 *      invalido ni mutarse despues.
 *   2. La persistencia salio a OrderRepository (src/Repositories).
 *   3. El calculo de precio salio a PricingStrategy (src/Pricing).
 * ============================================================================
 */

final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly string $patient,
        public readonly float $amount,
        public readonly string $patientType
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('El importe no puede ser negativo');
        }

        if (trim($patient) === '') {
            throw new InvalidArgumentException('El paciente es obligatorio');
        }
    }
}
