<?php

namespace App\Controller;

use App\Entity\TrainingType;
use App\Entity\TrainingTypeUser;
use App\Form\TrainingTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/training/type/")
 */
class TrainingTypeController extends AbstractController
{
    /**
     * @Route("create", name="create_training_type")
     */
    public function create(Request $request)
    {
        $trainingType = new TrainingType;
        $form = $this->createForm(TrainingTypeType::class, $trainingType);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trainingType = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($trainingType);
            $em->flush();

            return $this->redirectToRoute("show_training_type", [
                'trainingType' => $trainingType->getId(),
            ]);
        }

        return $this->render('trainingType/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("edit/{trainingType}", name="edit_training_type")
     */
    public function edit(Request $request, TrainingType $trainingType)
    {
        $form = $this->createForm(TrainingTypeType::class, $trainingType);
        $preUsers = $trainingType->getActiveTrainingTypeUsers();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $trainingType = $form->getData();
            $postUsers = $form->get('trainingTypeUsers')->getData();

            $now = new \DateTime();
            $preUsersList = [];
            foreach ($preUsers as $preUser) {
                $preUsersList[] = $preUser->getUser();
                if (!in_array($preUser->getUser(), $postUsers)) {
                    $preUser->setActiveUntil($now);
                }
            }
            foreach ($postUsers as $postUser) {
                if (!in_array($postUser, $preUsersList)) {
                    $trainingTypeUser = new TrainingTypeUser();
                    $trainingTypeUser->setUser($postUser);
                    $trainingTypeUser->setActiveSince($now);
                    $trainingTypeUser->setTrainingType($trainingType);
                    // TODO: improve role handling
                    $trainingTypeUser->setRole('admin');
                    $em->persist($trainingTypeUser);
                }
            }

            $em->persist($trainingType);
            $em->flush();
        }

        return $this->render('trainingType/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("show/{trainingType}", name="show_training_type")
     */
    public function show(TrainingType $trainingType)
    {
        return $this->render('trainingType/show.html.twig', [
            'trainingType' => $trainingType
        ]);
    }

    /**
     * @Route("list", name="list_training_types")
     */
    public function showAll()
    {
        return $this->render('trainingType/list.html.twig', [
            'trainingTypes' => $this->getDoctrine()->getManager()->getRepository(TrainingType::class)->findAll()
        ]);
    }
}
