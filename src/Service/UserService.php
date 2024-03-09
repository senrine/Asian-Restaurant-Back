<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $hasher)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    public function signup(array $data): array
    {
        $user = new User();
        if(($data["email"] !== null) && $data["name"] !== null && $data["password"] !== null){
            $user->setEmail($data["email"]);
            $user->setName($data["name"]);
            $hashedPassword = $this->hasher->hashPassword($user, $data["password"]);
            $user->setPassword($hashedPassword);

            $this->userRepository->save($user);

            return $user->serialize();
        } else {
            return [];
        }
    }

    public function login(array $data): array
    {

    }
}