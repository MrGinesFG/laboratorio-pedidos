<?php
/**
 * ============================================================================
 *  ORDER FACADE — FACADE (estructural)
 * ============================================================================
 *  Reemplaza a OrderService::procesarPedidoCompleto(), que validaba,
 *  calculaba, guardaba, notificaba, generaba reporte e imprimia HTML,
 *  todo en un solo metodo con mas de 6 responsabilidades.
 *
 *  createOrder() ORQUESTA los servicios que ya existen gracias a los otros
 *  patrones (Strategy, Factory, Observer). No decide reglas de negocio: si
 *  empezara a decidir, dejaria de ser una fachada.
 * ============================================================================
 */
final class OrderFacade
{
    public function __construct(
        private OrderValidator $validator,
        private NotificationSender $notifier,
        private OrderSubject $events,
        private OrderRepository $orders
    ) {
    }

    /**
     * Pedidos listos para mostrar: el total ya viene calculado con la
     * PricingStrategy que corresponde, asi la vista no calcula nada.
     *
     * @return array<int, array{id: int, paciente: string, total: float}>
     */
    public function listOrders(): array
    {
        return array_map(
            fn (Order $order): array => [
                'id'       => $order->id,
                'paciente' => $order->patient,
                'total'    => (new PriceCalculator(
                    PricingStrategyFactory::forPatientType($order->patientType)
                ))->calculate($order->amount),
            ],
            $this->orders->findAll()
        );
    }

    public function createOrder(
        int $id,
        string $paciente,
        float $monto,
        string $tipoPaciente,
        string $tipoNotificacion,
        string $destino
    ): Order {
        $this->validator->validate($paciente, $monto);

        $strategy = PricingStrategyFactory::forPatientType($tipoPaciente);
        $total = (new PriceCalculator($strategy))->calculate($monto);

        $order = new Order($id, $paciente, $total, $tipoPaciente);
        $this->orders->save($order);

        $this->notifier->enviar($tipoNotificacion, $destino, "Pedido {$id} por $ {$total}");
        $this->events->notify($order);

        return $order;
    }
}