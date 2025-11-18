<?php

namespace App\DataFixtures;

use App\Entity\Hamster;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $admin = $this->createUser('admin@hamster.com', 'admin1234', ['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->createDefaultHamstersForUser($admin, $manager);

        $user = $this->createUser('user@hamster.com', 'password123', ['ROLE_USER']);
        $manager->persist($user);
        $this->createDefaultHamstersForUser($user, $manager);

        $manager->flush();
    }

    public function createUser(string $email, string $password, array $roles): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setGold(500);
        return $user;
    }

    private function createDefaultHamstersForUser(User $user, ObjectManager $manager): void
    {
        for ($i = 1; $i <= 4; $i++) {
            $faker = \Faker\Factory::create('fr_FR');
            $hamster = new Hamster();
            $hamster->setName($faker->firstName);
            $hamster->setGender($i <= 2 ? 'm' : 'f');
            $hamster->setAge($faker->numberBetween(0, 500));
            $hamster->setHunger($faker->numberBetween(0, 100));
            $hamster->setActive(true);
            $hamster->setOwner($user);
            $manager->persist($hamster);
        }
    }
}
