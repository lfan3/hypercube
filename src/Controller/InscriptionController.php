<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserPhoto;
use App\Form\InscriptionType;
use App\Form\UserPhotoType;
use App\Service\UploadFileService;
use App\Service\ProfileService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class InscriptionController extends BaseController
{
    private $photoPath = '/avatars/default.png';
    /**
     * @Route("/inscription", name="inscription")
     */
    public function index(Request $request, ObjectManager $om, UserPasswordEncoderInterface $encoder,SessionInterface $session, UploadFileService $uploadFileService)
    {
        //user form
        $user = new User();
        $userPhoto = new UserPhoto();
        
   //
        $form = $this->createForm(InscriptionType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if(is_null($session->get('foo')) )
                $userPhoto->setPhotoLink($this->photoPath);
            else{
                $userPhoto->setPhotoLink($session->get('foo'));
            }
            
            $userPhoto->setProfilePhoto(true);
            $om->persist($userPhoto);

            $passwordHash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordHash);
            $user->setRoles("ROLE_USER");
            $user->addUserPhoto($userPhoto);
            
            $om->persist($user);
            $om->flush();
    
            return $this->redirectToRoute('app_login');
        }

        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
            'form'=>$form->createView(),
        ]);
    }

     /**
     * @Route("/inscription_test", name="inscription_test" )
     */
    public function test(Request $request, SessionInterface $session, ObjectManager $om, UserPasswordEncoderInterface $encoder, UploadFileService $uploadFileService)
    {
        //get the file of formdata from ajax
        $file = $request->files->get('avatarImg');
 
        $uniqFileName = $uploadFileService->upload($file);
        $this->photoPath = '/avatars/'.$uniqFileName;
        $session->set('foo', '/avatars/'.$uniqFileName);

        return new JsonResponse(['data'=>$this->photoPath]);
    }

         /**
     * @Route("/abc", name="inscription_test_index" )
     */
    public function test_index(Request $request, SessionInterface $session, ObjectManager $om, UserPasswordEncoderInterface $encoder, UploadFileService $uploadFileService)
    {
        //get the file of formdata from ajax


        return $this->render('test/index.html.twig');
    }

     /**
     * @Route("/sendEmail", name="sendEmail" )
     */
    public function sendMail(MailerInterface $mailer)
    {
        $email = (new TemplatedEmail())
        ->from('lulu@exemple.com')
        ->to('fifidemacici@gmail.com')
        ->subject('welcome')
        ->htmlTemplate('email/index.html.twig');

        $mailer->send($email);
        
        //return $this->render('email/index.html.twig');
        return $this->render('email/changeMdp.html.twig');
    }
}
