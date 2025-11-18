<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Hamster;
use App\Repository\HamsterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;


final class HamsterController extends AbstractController
{
    #[Route('/api/hamsters', name: 'app_hamsters', methods: ['GET'])]
    public function getAllHamsters(HamsterRepository $repo): JsonResponse
    {
        $listHamsters = $repo->findAll();
        return $this->json([
            'listHamsters' => $listHamsters,
        ], Response::HTTP_OK, [], ['groups' => 'AllHamsters']);
    }

    #[Route('/api/hamsters/{id}', name: 'hamsters_by_id', methods: ['GET'])]
    public function getHamstersById(Hamster $hamsters): JsonResponse
    {
        return $this->json([
            'hamsters' => $hamsters,
        ], Response::HTTP_OK, [], ['groups' => 'AllHamsters']);
    }
    

    #[Route('/api/hamsters/reproduce', name: 'hamsters_reproduce', methods: ['POST'])]
    public function reproduce(Request $request, HamsterRepository $repo, UserRepository $userRepo, EntityManagerInterface $em): JsonResponse {
        
        $data = json_decode($request->getContent(), true);

        $id1 = $data['idHamster1'] ?? null;
        $id2 = $data['idHamster2'] ?? null;

        if ($id1 === null || $id2 === null) {
            return $this->json(['error' => 'idHamster1 et idHamster2 sont requis'], 400);
        }

        $securityUser = $this->getUser();
        if (!$securityUser) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $user = $userRepo->findOneBy(['email' => $securityUser->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 500);
        }

        $parent1 = $repo->find($id1);
        $parent2 = $repo->find($id2);

        if (!$parent1 || !$parent2) {
            return $this->json(['error' => 'Un des hamsters n’existe pas'], 404);
        }

        if ($parent1->getOwner()->getId() !== $user->getId() || $parent2->getOwner()->getId() !== $user->getId()
        ) {
            return $this->json(['error' => 'Vous ne pouvez reproduire que vos propres hamsters'], 403);
        }

        if (!$parent1->isActive() || !$parent2->isActive()) {
            return $this->json(['error' => 'Les deux hamsters doivent être actifs'], 400);
        }

        if ($parent1->getGender() === $parent2->getGender()) {
            return $this->json(['error' => 'Les deux hamsters doivent être de sexes opposés'], 400);
        }

        $faker = \Faker\Factory::create('fr_FR');
        $baby = new Hamster();
        $baby->setName($faker->firstName);
        $baby->setGender(random_int(0, 1) ? 'm' : 'f');
        $baby->setAge(0);
        $baby->setHunger(100);
        $baby->setActive(true);
        $baby->setOwner($user);
        $em->persist($baby);

        foreach ($user->getHamsters() as $hamster) {
            if (!$hamster->isActive()) {
                continue;
            }

            $hamster->setAge($hamster->getAge() + 5);
            $hamster->setHunger($hamster->getHunger() - 5);

            if ($hamster->getAge() > 500 || $hamster->getHunger() < 0) {
                $hamster->setActive(false);
            }
        }
        $em->flush();

        return $this->json(
            $baby, Response::HTTP_CREATED, [], ['groups' => 'AllHamsters']);
    }

    #[Route('/api/hamsters/{id}/feed', name: 'hamsters_feed', methods: ['POST'])]
    public function feed(Hamster $hamster, UserRepository $userRepo, EntityManagerInterface $em): JsonResponse {

        $securityUser = $this->getUser();
        if (!$securityUser) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $user = $userRepo->findOneBy(['email' => $securityUser->getUserIdentifier()]);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 500);
        }

        if ($hamster->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Vous ne pouvez nourrir que vos propres hamsters'], 403);
        }

        if (!$hamster->isActive()) {
            return $this->json(['error' => 'Ce hamster n’est pas actif'], 400);
        }

        $currentHunger = $hamster->getHunger();

        if ($currentHunger >= 100) {
            return $this->json(['error' => 'Ce hamster est déjà rassasié'], 400);
        }

        $cost = 100 - $currentHunger;

        if ($user->getGold() < $cost) {
            return $this->json(['error' => 'Pas assez de gold pour nourrir ce hamster'], 400);
        }

        $hamster->setHunger(100);
        $user->setGold($user->getGold() - $cost);
        $this->ageAllHamstersOfUser($user);


        $em->flush();

        return $this->json(['gold' => $user->getGold(),], Response::HTTP_OK);
    }

    private function ageAllHamstersOfUser(User $user): void
    {
        foreach ($user->getHamsters() as $hamster) {
            if (!$hamster->isActive()) {
                continue;
            }

            $hamster->setAge($hamster->getAge() + 5);
            $hamster->setHunger($hamster->getHunger() - 5);

            if ($hamster->getAge() > 500 || $hamster->getHunger() < 0) {
                $hamster->setActive(false);
            }
        }
    }
    
}
