<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\MenuRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

class OrderService
{
    private OrderRepository $orderRepository;
    private MenuRepository $menuRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository, MenuRepository $menuRepository, UserRepository $userRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->menuRepository = $menuRepository;
        $this->userRepository = $userRepository;
    }

    public function getOrders(): array
    {
        $orders = $this->orderRepository->findAll();
        $serialized_orders = [];
        foreach ($orders as $order) {
            $serialized_orders[] = $order->serialize();
        }
        return $serialized_orders;
    }

    public function getOrderByID(int $id): array
    {
        return $this->orderRepository->findOneById($id)->serialize();
    }

    public function createOrder(array $data) :array
    {
        if ($data["menuId"] === null || $data["userId"] === null || $data["totalPrice"] === null) {
            return [];
        }

        $user = $this->userRepository->findOneBy(["id" => $data["userId"]]);

        $order = new Order();

        $order->setTotalPrice($data["totalPrice"]);
        $order->setUser($user);

        foreach ($data["menuId"] as $id) {
            $menu = $this->menuRepository->findOneById($id);
            $order->addMenu($menu);
        }

        $date = new \DateTime();
        $order->setDate($date);

        $this->orderRepository->save($order);

        return $order->serialize();
    }

    public function updateOrder(array $data, int $id): array
    {
        $order = $this->orderRepository->findOneById($id);

        if ($data["menuId"] !== null) {
            foreach ($data["menuId"] as $id) {
                $menu = $this->menuRepository->findOneById($id);
                $order->addMenu($menu);
            }
        }
        if ($data["userId"] !== null) {
            $user = $this->userRepository->findOneBy(["id" => $data["userId"]]);
            $order->setUser($user);
        }
        if ($data["totalPrice"] !== null) {
            $order->setTotalPrice($data["totalPrice"]);
        }

        $date = new \DateTime();
        $order->setDate($date);

        $this->orderRepository->save($order);

        return $order->serialize();
    }
}