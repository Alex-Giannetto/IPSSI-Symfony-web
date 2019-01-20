<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;

class UserManager
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Return the user who have the given in parameter id
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * Return all the user
     * @return array|null
     */
    public function getAllUser(): ?array
    {
        return $this->userRepository->findAll();
    }

}