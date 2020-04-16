<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public const DEFAULT_USER_LOGIN = 'foo';

    public const DEFAULT_USER_EMAIL = 'foo@example.com';

    public const DEFAULT_USER_PASSWORD = 'bar';

    public function load(ObjectManager $manager): void
    {
        $this->createUser($manager, self::DEFAULT_USER_LOGIN, self::DEFAULT_USER_EMAIL ,self::DEFAULT_USER_PASSWORD, ['ROLE_FOO']);
    }

    /**
     * Create user fixtures
     *
     * @param ObjectManager $manager
     * @param string $login
     * @param string $email
     * @param string $password
     * @param string[] $roles
     */
    private function createUser(ObjectManager $manager, string $login, string $email, string $password, array $roles): void
    {
        $userEntity = new User();
        $userEntity->setUsername($login);
        $userEntity->setEmail($email);
        $userEntity->setPlainPassword($password);
        $userEntity->setRoles($roles);
        $userEntity->setEnabled(true);
        $manager->persist($userEntity);
        $manager->flush();
    }
}
