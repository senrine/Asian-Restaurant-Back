<?php

namespace App\Controller;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order_all', methods: ["GET"])]
    public function getOrders(OrderService $orderService): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $orderService->getOrders(),
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_id', methods: ["GET"])]
    public function getOrderByID(OrderService $orderService, $id): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $orderService->getOrderByID($id),
        ]);
    }

    #[Route('/order', name: 'app_order_create', methods: ["POST"])]
    public function createOrder(OrderService $orderService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $orderService->createOrder($data);

        return $this->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_update', methods: ["PUT"])]
    public function updateOrder(OrderService $orderService, Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $orderService->updateOrder($data, $id);

        return $this->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_delete_line', methods: ["PATCH"])]
    public function deleteLine(OrderService $orderService, Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $order = $orderService->deleteOrMinusOneMenu($id, $data);

        return $this->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_delete', methods: ["DELETE"])]
    public function deleteOrder(OrderService $orderService, $id): JsonResponse
    {
        $order = $orderService->deleteOrder($id);

        return $this->json([
            'data' => "Order deleted successfully",
        ]);
    }

}
