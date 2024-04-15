<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\MenuRepository;
use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

class OrderService
{
    private OrderRepository $orderRepository;
    private MenuRepository $menuRepository;
    private UserRepository $userRepository;
    private OrderLineRepository $orderLineRepository;

    public function __construct(OrderRepository $orderRepository, MenuRepository $menuRepository, UserRepository $userRepository, OrderLineRepository $orderLineRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->menuRepository = $menuRepository;
        $this->userRepository = $userRepository;
        $this->orderLineRepository = $orderLineRepository;
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

    public function createOrder(array $data): array
    {
        if ($data["menuId"] === null || $data["userId"] === null) {
            return [];
        }

        $user = $this->userRepository->findOneBy(["id" => $data["userId"]]);

        $order = new Order();

        $order->setUser($user);

        $date = new \DateTime();
        $order->setDate($date);

        $occurences = array_count_values($data["menuId"]);
        foreach ($occurences as $id => $count) {
            $orderLine = new OrderLine();
            $menu = $this->menuRepository->findOneById($id);
            $orderLine->setMenu($menu);
            $orderLine->setQuantity($count);
            $order->setTotalPrice($order->getTotalPrice() + $menu->getPrice() * $count);
            $this->orderLineRepository->save($orderLine);
            $order->addOrderLine($orderLine);
        }
        $this->orderRepository->save($order);

        return $order->serialize();
    }

    public function updateOrder(array $data, int $id): array
    {
        $order = $this->orderRepository->findOneById($id);

        if (array_key_exists("menuId", $data)) {
            $occurences = array_count_values($data["menuId"]);
            foreach ($occurences as $id => $count) {
                $menu = $this->menuRepository->findOneById($id);
                $line = $this->getOrderLineByMenuOrFalse($order, $menu);
                if ($line) {
                    $line->setQuantity($line->getQuantity() + $count);
                    $this->orderLineRepository->save($line);
                    $order->addOrderLine($line);
                } else {
                    $line = new OrderLine();
                    $line->setMenu($menu);
                    $line->setQuantity($count);
                    $line->setOrderParent($order);
                    $this->orderLineRepository->save($line);
                    $order->addOrderLine($line);

                }
                $order->setTotalPrice($menu->getPrice() * $count + $order->getTotalPrice());

            }
        }

        if (array_key_exists("userId", $data)) {
            $user = $this->userRepository->findOneBy(["id" => $data["userId"]]);
            $order->setUser($user);
        }


        $date = new \DateTime();
        $order->setDate($date);

        $this->orderRepository->save($order);

        return $order->serialize();
    }

    public function deleteOrder(int $id): array
    {
        $order = $this->orderRepository->findOneById($id);
        $this->orderRepository->remove($order);

        return $order->serialize();
    }

    private function getOrderLineByMenuOrFalse(Order $order, Menu $menu): OrderLine|bool
    {
        foreach ($order->getOrderLines() as $line) {
            if ($line->getMenu() === $menu) {
                return $line;
            }
        }
        return false;
    }

    public function deleteOrMinusOneMenu(int $id, array $data): array
    {
        $order = $this->orderRepository->findOneById($id);
        $menu = $this->menuRepository->findOneById($data["menuId"]);
        $delete = $data["delete"];
        $minusOne = $data["minusOne"];
        $line = $this->getOrderLineByMenuOrFalse($order, $menu);

        if ($line && $minusOne) {
            $line->setQuantity($line->getQuantity() - 1);
            $this->orderLineRepository->save($line);
            $order->setTotalPrice($order->getTotalPrice() - $line->getMenu()->getPrice());
        }
        if ($line && $delete) {
            $order->removeOrderLine($line);
            $priceOfLine = $line->getMenu()->getPrice()*$line->getQuantity();
            $order->setTotalPrice($order->getTotalPrice() - $priceOfLine);

            $this->orderLineRepository->remove($line);
        }
        $this->orderRepository->save($order);
        return $order->serialize();
    }

}