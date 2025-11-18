<?php

namespace App\Controller;

use App\Entity\Hamster;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Email invalide'], 400);
        }

        if (strlen((string) $password) < 8) {
            return $this->json(['error' => 'Mot de passe trop court (min 8)'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, $password));
        $user->setGold(500);

        $em->persist($user);

        $faker = \Faker\Factory::create('fr_FR');

        $hamsters = [];

        for ($i = 1; $i <= 4; $i++) {
            $hamster = new Hamster();
            $hamster->setName($faker->firstName);
            $hamster->setGender($i <= 2 ? 'm' : 'f');
            $hamster->setAge($faker->numberBetween(0, 500));
            $hamster->setHunger($faker->numberBetween(0, 100));
            $hamster->setActive(true);
            $hamster->setOwner($user);

            $em->persist($hamster);
            $hamsters[] = $hamster;
        }

        $em->flush();

        $hamstersData = array_map(function (Hamster $h) {
            return [
                'id'      => $h->getId(),
                'name'    => $h->getName(),
                'gender'  => $h->getGender(),
                'age'     => $h->getAge(),
                'hunger'  => $h->getHunger(),
                'active'  => $h->isActive(),
            ];
        }, $hamsters);

        return $this->json([
            'id'       => $user->getId(),
            'email'    => $user->getEmail(),
            'roles'    => $user->getRoles(),
            'gold'     => $user->getGold(),
            'hamsters' => $hamstersData,
        ], 201);
    }

    #[Route('/api/delete/{id}', name: 'api_delete_user', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        return $this->json([
            'message' => 'Utilisateur et hamsters supprimés avec succès',
        ]);
    }

    #[Route('/api/user', name: 'api_user', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        return $this->json(
            $user, 
            Response::HTTP_OK,
            [],
            ['groups' => 'CurrentUser']
        );
    }

}
