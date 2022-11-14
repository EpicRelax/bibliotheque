<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordHasher;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LivreFixtures extends Fixture
{   
    private $encode;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encode = $encoder;
    }
    public function load(ObjectManager $manager): void
    {   
        $faker = Factory::create("fr_FR");
        for($i = 0;$i<5;$i++){
            $user = new User();
            $hash = $this->encode->encodePassword($user, "test");
            $user->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setBirthDate($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'))
                ->setAdresse($faker->streetAddress())
                ->setCodeP($faker->postcode())
                ->setEmail($faker->email())            
                ->setPassword($hash)
                ->setAvatar("https://picsum.photos/id/237/200/300");
                $manager->persist($user);
                for ($k = 0; $k < 3; $k++) {
                    $category = new Category();
                    $category->setNom($faker->safeColorName);
                    $manager->persist($category);

                    for($j=0;$j<rand(1, 5);$j++){
                        $livre = new Livre();
                        $livre->setAuteur($faker->name())
                        ->setTitre($faker->word())
                        ->setDateSortie($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'));
                        $livre->setUser($user);
                        $livre->setCategory($category);
                        $manager->persist($livre);
                    }
                }
        }
        $manager->flush();
    }
}
