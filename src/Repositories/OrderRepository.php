<?php
/**
 * ============================================================================
 *  ORDER REPOSITORY (persistencia de pedidos)
 * ============================================================================
 *  Unico lugar que conoce como se guarda un Order. Si cambia el motor de base
 *  de datos, cambia esta clase y no el modelo (SRP).
 *
 *  La consulta usa parametros con nombre (:id, :patient, :amount) en vez de
 *  concatenar valores: elimina la inyeccion SQL del guardar() original.
 * ============================================================================
 */

final class OrderRepository
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * Persistencia simulada en memoria (no es parte de la deuda del TP): en
     * el sistema real aca va un SELECT. Son los mismos pedidos de ejemplo que
     * antes estaban escritos dentro de la vista.
     *
     * @return Order[]
     */
    public function findAll(): array
    {
        return [
            new Order(1, 'Juan Perez', 15000, 'obra_social'),
            new Order(2, 'Ana Gomez', 22000, 'particular'),
        ];
    }

    public function save(Order $order): void
    {
        $this->connection->ejecutar(
            'INSERT INTO orders (id, patient, amount) VALUES (:id, :patient, :amount)',
            [
                'id'      => $order->id,
                'patient' => $order->patient,
                'amount'  => $order->amount,
            ]
        );
    }
}
