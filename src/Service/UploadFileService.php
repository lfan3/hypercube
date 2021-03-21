<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserPhoto;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UploadFileService
{
    private $targetDirectory;
    //for accessing the user
    private $security;
    private $slugger;
    private $em;

    public function __construct($targetDirectory, SluggerInterface $slugger, EntityManagerInterface $em, Security $security)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->em = $em;
        $this->security = $security;
    }

    public function upload(UploadedFile $file):string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $this->slugger->slug($originalName);
        $uniqFileName = $safeName.'-'.uniqid().'.'.$file->guessExtension();
        try{
            $file->move($this->targetDirectory, $uniqFileName);
        }catch(FileException $e)
        {
            //handler exception
        }
        return $uniqFileName;
    }

   

    public function UploadImgResetDefaultAvatar(String $uniqFileName, UserPhoto $defaultUserPhoto):string
    {
        $user = $this->security->getUser();
        $relativePath = '/avatars/'.$uniqFileName;
        $userPhoto = new UserPhoto();
        $userPhoto->setPhotoLink($relativePath);
        $userPhoto->setProfilePhoto(true);
        $userPhoto->setUser($user);
        $defaultUserPhoto->setProfilePhoto(false);

        $this->em->persist($userPhoto);
        $this->em->flush();
        return $relativePath;
    }

 

}