<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@admin.de')
            ->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    '1234567890'
                )
            )
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstName('admin')
            ->setlastName('admin');

        $userTypeCustomer = new UserType();
        $userTypeCustomer->setType(UserType::CUSTOMER);

        $userTypeAgent = new UserType();
        $userTypeAgent->setType(UserType::AGENT);

        $manager->persist($user);
        $manager->persist($userTypeCustomer);
        $manager->persist($userTypeAgent);
        $manager->flush();
    }
}
