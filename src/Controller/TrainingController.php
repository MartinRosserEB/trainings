<?php

namespace App\Controller;

use App\Entity\Attendance;
use App\Entity\Training;
use App\Entity\User;
use App\Form\TrainingType as TrainingTypeForm;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/training/")
 */
class TrainingController extends AbstractController
{
    /**
     * @Route("create", name="create_training")
     */
    public function create(Request $request, UserInterface $user)
    {
        $training = new Training;
        $form = $this->createForm(TrainingTypeForm::class, $training);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $training = $form->getData();
            if ($form->get('public')->getData()) {
                $training->setPublic(true);
            }
            $training->setCreator($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();

            return $this->redirectToRoute("show_training", [
                'training' => $training->getId(),
            ]);
        }

        return $this->render('training/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("edit/{training}", name="edit_training")
     */
    public function edit(Request $request, Training $training)
    {
        $form = $this->createForm(TrainingTypeForm::class, $training);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $training = $form->getData();
            $public = false;
            if ($form->get('public')->getData()) {
                $public = true;
            }
            $training->setPublic($public);
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();
        }

        return $this->render('training/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("show/{training}", name="show_training")
     */
    public function show(Training $training)
    {
        return $this->render('training/show.html.twig', [
            'training' => $training
        ]);
    }

    /**
     * @Route("public/show/{hash}", name="show_training_by_hash")
     */
    public function showByHash($hash)
    {
        $training = $this->getDoctrine()->getManager()->getRepository(Training::class)->findOneByPublic($hash);

        return $this->render('training/show_by_hash.html.twig', [
            'training' => $training,
            'hash' => $hash
        ]);
    }

    /**
     * @Route("public/getUser/{mail}", name="find_user_by_mail")
     */
    public function findByMail($mail, SerializerInterface $serializer)
    {
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findByEmail($mail);

        return new JsonResponse($serializer->serialize($users, 'json', ['groups' => 'public']));
    }

    /**
     * @Route("public/register/", name="register_user")
     */
    public function registerUser(Request $request, SerializerInterface $serializer)
    {
        $user = new User();
        $user->setFirstName($request->get('firstName'));
        $user->setFamilyName($request->get('familyName'));
        $user->setEmail($request->get('email'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'user' => $serializer->serialize($user, 'json', ['groups' => 'public']),
        ]);
    }

    /**
     * @Route("public/enlist/{hash}/{user}", name="enlist_user_for_training")
     */
    public function enlistUserForTraining(Request $request, User $user, $hash, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);
        $attendance = (new Attendance())
            ->setTraining($training)
            ->setUser($user)
            ->setEnlistingIp($request->getClientIp())
            ->setEnlistingTimestamp(new \DateTime());
        $em->persist($attendance);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'user' => $serializer->serialize($user, 'json', ['groups' => 'public']),
            'training' => $serializer->serialize($training, 'json', ['groups' => 'public']),
        ]);
    }
}
