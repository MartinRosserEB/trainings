<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("edit/{user}", name="edit_user")
     */
    public function edit(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("list_users");
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
