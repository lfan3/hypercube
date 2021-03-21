<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\UserPhoto;
use App\Form\ProfileType;
use App\Form\UserPhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UploadFileService;
use App\Service\ProfileService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormInterface;


 /** Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends BaseController
{
    /**
     * @Route("/profile/{userId}", name="profile")
     */
    public function index(Request $request, $userId, EntityManagerInterface $em, UploadFileService $uploadFileService, ProfileService $profileService): Response
    {
        //todo: if the formulale valide, we will update the new image and close the modal
        //if the not valide, keep modal open and show error
        //need to render only the formule on the modal, so seperate the formule
        // $currentUser = $this->getUser();
        // $userId = $currentUser->getId();
        $defaultUserPhoto = $profileService->getDefaultUserPhoto($userId);
        $avatarSrc = $defaultUserPhoto->getPhotoLink();
 
        $form = $this->createForm(UserPhotoType::class, $defaultUserPhoto);
        $form->handleRequest($request);
        //the case the formule validation is good
        if($form->isSubmitted() && $form->isValid()){
            $image = $form->get("avatar")->getData();
             //$destination = $this->getParameter('kernel.project_dir').'/public/avatars/';
            $uniqFileName = $uploadFileService->upload($image);
            $filePath = $uploadFileService->UploadImgResetDefaultAvatar($uniqFileName, $defaultUserPhoto);

            return $this->createApiResponse($filePath);
        }
        //the case the formule validation is not right
   
        if($request->isXmlHttpRequest()){

            $errors = $this->getErrorsFromForm($form);
        
            return $this->createApiResponse($errors, 400);
        }
        
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'avatarSrc' => $avatarSrc,
            'profileForm'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/profile/edit/{userId}", name="editProfile", methods="POST")
     */
    public function editProfile(Request $req, EntityManagerInterface $em, string $userId, ProfileService $profileService)
    {
        //request est lié avec name attribute
        //dd($req->request->all());
   
        $username = $req->request->get('username');
        $email = $req->request->get('email');
        $id = intval($userId);

        $profileService->updateUser($id, $username, $email);
    }



    /**
     * Returns an associative array of validation errors
     * important + utile
     * {
     *     'firstName': 'This value is required',
     *     'subForm': {
     *         'someField': 'Invalid value'
     *     }
     * }
     *
     * @param FormInterface $form
     * @return array|string
     */
    protected function getErrorsFromForm(FormInterface $form)
    {
        foreach ($form->getErrors() as $error) {
            // only supporting 1 error per field
            // and not supporting a "field" with errors, that has more
            // fields with errors below it
            return $error->getMessage();
        }

        $errors = array();
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childError = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childError;
                }
            }
        }

        return $errors;
    }
}
