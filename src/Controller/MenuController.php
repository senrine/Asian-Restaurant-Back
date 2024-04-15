<?php

namespace App\Controller;

use App\Service\MenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu_read', methods: ["GET"])]
    public function getMenus(MenuService $menuService): JsonResponse
    {
        return $this->json([
            'data' => $menuService->getMenus()
        ]);
    }

    #[Route('/menu/{id}', name: 'app_menu_id', methods: ["GET"])]
    public function getMenuById(MenuService $menuService, $id): JsonResponse
    {
        return $this->json([
            'data' => $menuService->getMenuById($id)
        ]);
    }

    #[Route('/menu', name: 'app_menu_create', methods: ["POST"])]
    public function createMenu(MenuService $menuService, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $menu = $menuService->createMenu($data);

        return $this->json([
            'data' => $menu
        ]);
    }

    #[Route('/menu/{id}', name: 'app_menu_delete', methods: ["DELETE"])]
    public function deleteMenu(MenuService $menuService, $id): JsonResponse
    {
        return $this->json([
            'data' => $menuService->deleteMenu($id)
        ]);
    }

    #[Route('/menu/{id}', name: 'app_menu_update', methods: ["PUT"])]
    public function updateMenu(MenuService $menuService, $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        return $this->json([
            'data' => $menuService->updateMenu($data,$id)
        ]);
    }

}
