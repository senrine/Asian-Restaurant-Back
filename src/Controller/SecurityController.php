<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SecurityController extends AbstractController
{
    #[Route('/signup', name: 'app_user_signup', methods: ["POST"])]
    public function signup(Request $request, UserService $userService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userService->signup($data);
        return $this->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    #[Route('/login', name: 'app_user_login', methods: ["POST"])]
    public function login(#[CurrentUser] ?User $user, UserService $userService): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'success' => false,
                'message' => "Invalid credentials"
                ]);
        }

        return $this->json([
            "authenticated" => true,
            "as" => $user->serialize()
        ]);
    }

    #[Route('/logout', name: 'app_user_logout', methods: ["POST"])]
    public function logout() : void
    {
    }
}
