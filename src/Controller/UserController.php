<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/user/")
 */
class UserController extends AbstractController
{
    /**
     * @Route("list", name="list_users")
     */
    public function showUsers()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('user/list.html.twig', [
            'users' => $em->getRepository(User::class)->findAll(),
        ]);
    }

    /**
     * @Route("create", name="create_user")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request, UserService $userSrv)
    {
        $form = $userSrv->handleForm($request, new User());

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute("edit_user", [
                'user' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("edit/{user}", name="edit_user")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, User $user, UserService $userSrv)
    {
        return $this->render('user/edit.html.twig', [
            'form' => $userSrv->handleForm($request, $user)->createView()
        ]);
    }

    /**
     * @Route("editme", name="edit_own_user")
     */
    public function editme(Request $request, UserInterface $user, UserService $userSrv)
    {
        return $this->render('user/edit.html.twig', [
            'form' => $userSrv->handleForm($request, $user)->createView()
        ]);
    }
}
