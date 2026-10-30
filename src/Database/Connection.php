<?php
/**
 * ============================================================================
 *  CONEXION A LA BASE DE DATOS — SINGLETON (creacional)
 * ============================================================================
 *  Antes: obtener() devolvia un `new Connection()` en cada llamada (40
 *  consultas = 40 conexiones), las credenciales llegaban por constantes
 *  globales y la excepcion de ejecutar() se tragaba en silencio.
 *
 *  Ahora:
 *   1. Constructor privado + instancia estatica + getInstance(): hay UNA
 *      sola conexion por request, y nadie puede hacer `new Connection()`.
 *   2. La configuracion se inyecta una sola vez en getInstance($config);
 *      despues, quien la necesita llama getInstance() sin parametros.
 *   3. Si algo falla, el error se propaga (fallar rapido y fuerte).
 *
 *  Singleton sirve para conexion, logger y configuracion. NO para guardar el
 *  pedido actual ni el usuario logueado: ahi seria una variable global
 *  disfrazada.
 * ============================================================================
 */

final class Connection
{
    private static ?Connection $instance = null;

    /** Almacen en memoria para poder correr la demo sin MySQL levantado. */
    private array $tablaEnMemoria = [];

    private function __construct(private array $config)
    {
    }

    public static function getInstance(?array $config = null): Connection
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new LogicException(
                    'La conexion todavia no fue configurada: llamar primero a getInstance($config).'
                );
            }
            self::$instance = new Connection($config);
        }

        return self::$instance;
    }

    public function ejecutar(string $sql): void
    {
        if (trim($sql) === '') {
            throw new InvalidArgumentException('La consulta SQL no puede estar vacia');
        }

        $this->tablaEnMemoria[] = $sql;
    }

    public function consultas(): array
    {
        return $this->tablaEnMemoria;
    }
}
