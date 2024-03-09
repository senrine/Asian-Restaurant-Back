<?php

namespace App\Service;

use App\Entity\Menu;
use App\Repository\MenuRepository;

class MenuService
{

    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function getMenus(): array
    {
        $menus = $this->menuRepository->findAll();
        $serialized_menus = [];
        foreach ($menus as $menu) {
            $serialized_menus[] = $menu->serialize();
        }
        return $serialized_menus;
    }

    public function getMenuById(int $id): array
    {
        $menu = $this->menuRepository->findOneById($id);
        return $menu->serialize();
    }

    public function createMenu(array $data): array
    {
        $menu = new Menu();
        $menu->setName($data["name"]);
        $menu->setPrice($data["price"]);
        $menu->setDescription($data["description"]);
        $menu->setImage($data["image"]);

        $this->menuRepository->save($menu);

        return $menu->serialize();
    }

    public function deleteMenu(int $id): array
    {
        $menu = $this->menuRepository->findOneById($id);
        $this->menuRepository->remove($menu);

        return $menu->serialize();

    }

    public function updateMenu(array $data, int $id): array
    {
        $menu = $this->menuRepository->findOneById($id);

        if($data["name"]!== null){
            $menu->setName($data["name"]);
        }
        if($data["price"]!== null){
            $menu->setPrice($data["price"]);
        }
        if($data["image"]!== null){
            $menu->setImage($data["image"]);
        }
        if($data["description"]!== null){
            $menu->setDescription($data["description"]);
        }

        $this->menuRepository->save($menu);

        return $menu->serialize();
    }
}