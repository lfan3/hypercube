<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserPhoto;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProfileService
{
   
    private $security;
    private $em;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function getDefaultUserPhoto($userId):UserPhoto
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        $defaultUserPhoto = $this->em->getRepository(UserPhoto::class)->findOneBy(array(
            'user' => $user,
            'profilePhoto' => true
        ));
        return $defaultUserPhoto;
    }

    public function updateUser($userId, $username, $email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userId]);

        $user->setUsername($username);
        $user->setEmail($email);

        $this->em->persist($user);
        $this->em->flush();
    }
}