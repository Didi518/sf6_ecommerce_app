<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ){}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('Ouali');
        $admin->setFirstName('Kevin');
        $admin->setAddress('4 rue Menudière 2');
        $admin->setZipcode('30100');
        $admin->setCity('Alès');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'azerty')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <= 5; $usr++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setLastname($faker->lastName());
            $user->setFirstName($faker->firstName());
            $user->setAddress($faker->streetAddress());
            $user->setZipcode(str_replace(' ', '', $faker->postcode()));
            $user->setCity($faker->city());
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );

            $manager->persist($user);
        }

        $manager->flush();
    }
}
