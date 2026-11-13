<?php

use PHPUnit\Framework\TestCase;

/**
 * Base para las pruebas que tocan Connection.
 *
 * Connection es un Singleton: guarda su instancia en una propiedad estatica,
 * asi que "sobrevive" entre un test y el siguiente y un test podria contaminar
 * a otro. Por eso la reiniciamos con reflexion antes y despues de cada prueba.
 * (Es el costo de testear un Singleton, y esta documentado como consecuencia
 * asumida en docs/DEUDA-TECNICA.md.)
 */
abstract class ConnectionTestCase extends TestCase
{
    protected const CONFIG = ['host' => '', 'name' => '', 'user' => '', 'pass' => ''];

    protected function setUp(): void
    {
        $this->reiniciarConexion();
    }

    protected function tearDown(): void
    {
        $this->reiniciarConexion();
    }

    protected function reiniciarConexion(): void
    {
        (new ReflectionProperty(Connection::class, 'instance'))->setValue(null, null);
    }
}
