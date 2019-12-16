<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
  private $encoder;

  public function __construct(UserPasswordEncoderInterface $encoder)
  {
    $this->encoder = $encoder;
  }

  public function load(ObjectManager $manager)
  {
    $faker = Faker\Factory::create('fr_FR');

    $adminUser = new User();
    $adminUser->setActive(true)
      ->setRoles(['ROLE_ADMIN'])
      ->setBirthDate(new DateTime('1990-02-16'))
      ->setGender('Madame')
      ->setLogin('Bobby')
      ->setEmail('bobby@madmoizelle.com')
      ->setProfilePic($faker->imageUrl(150, 150))
      ->setPassword(
        $this->encoder->encodePassword(
          $adminUser,
          'Bobbybob57'
        )
      );

    $manager->persist($adminUser);

    for ($i = 0; $i < 70; $i++) {
      $user = new User();

      $login = $faker->userName;

      $user->setActive($faker->boolean(75))
        ->setBirthDate($faker->dateTimeBetween('-40 years', '-22 years'))
        ->setEmail($faker->email)
        ->setGender($faker->title())
        ->setLogin($login)
        ->setPassword($this->encoder->encodePassword(
          $user,
          'pass_' . $login
        ))
        ->setProfilePic($faker->imageUrl(150, 150));

      $manager->persist($user);
    }

    $manager->flush();
  }
}
