<?php

/** Singleton de conexion + OrderRepository (consultas parametrizadas). */
final class DatabaseTest extends ConnectionTestCase
{
    /** CP-DB-01 */
    public function testGetInstanceDevuelveSiempreLaMismaConexion(): void
    {
        $primera = Connection::getInstance(self::CONFIG);
        $segunda = Connection::getInstance();

        $this->assertSame($primera, $segunda);
    }

    /** CP-DB-02 (negativo) */
    public function testGetInstanceSinConfigurarLanzaExcepcion(): void
    {
        $this->expectException(LogicException::class);

        Connection::getInstance();
    }

    /** CP-DB-03 (negativo): el error se propaga, ya no se traga en silencio. */
    public function testEjecutarConSqlVacioLanzaExcepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Connection::getInstance(self::CONFIG)->ejecutar('   ');
    }

    /** CP-DB-04 */
    public function testEjecutarRegistraLaConsultaYSusParametros(): void
    {
        $conexion = Connection::getInstance(self::CONFIG);
        $conexion->ejecutar('SELECT 1', ['a' => 1]);

        $this->assertSame([['sql' => 'SELECT 1', 'params' => ['a' => 1]]], $conexion->consultas());
    }

    /** CP-DB-05 */
    public function testGuardarUsaParametrosConNombre(): void
    {
        $conexion = Connection::getInstance(self::CONFIG);
        (new OrderRepository($conexion))->save(new Order(7, 'Ana Gomez', 500.0, 'particular'));

        $consulta = $conexion->consultas()[0];

        $this->assertStringContainsString(':patient', $consulta['sql']);
        $this->assertSame(['id' => 7, 'patient' => 'Ana Gomez', 'amount' => 500.0], $consulta['params']);
    }

    /** CP-DB-06 (negativo): un intento de inyeccion SQL viaja como dato, nunca dentro del SQL. */
    public function testUnNombreMaliciosoNoSeConcatenaAlSql(): void
    {
        $malicioso = "x'); DROP TABLE orders;--";
        $conexion = Connection::getInstance(self::CONFIG);

        (new OrderRepository($conexion))->save(new Order(1, $malicioso, 100.0, 'particular'));

        $consulta = $conexion->consultas()[0];

        $this->assertStringNotContainsString('DROP', $consulta['sql']);
        $this->assertSame($malicioso, $consulta['params']['patient']);
    }
}
