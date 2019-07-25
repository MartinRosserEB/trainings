<?php

namespace App\Controller;

use App\Entity\TrainingType;
use App\Service\TrainingTypeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/training/type/")
 */
class TrainingTypeController extends AbstractController
{
    /**
     * @Route("create", name="create_training_type")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request, TrainingTypeService $trainingTypeSrv)
    {
        $trainingType = new TrainingType;
        $this->getDoctrine()->getManager()->persist($trainingType);
        $form = $trainingTypeSrv->handleForm($request, $trainingType);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute("show_training_type", [
                'trainingType' => $trainingType->getId(),
            ]);
        }

        return $this->render('trainingType/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("edit/{trainingType}", name="edit_training_type")
     */
    public function edit(Request $request, TrainingType $trainingType, TrainingTypeService $trainingTypeSrv, UserInterface $user)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $tTP = $trainingType->getActiveTrainingTypePersonFor($user);
            if ($tTP->getRole() !== 'ADMIN') {
                throw new AccessDeniedException('Access denied');
            }
        }

        $form = $trainingTypeSrv->handleForm($request, $trainingType);

        return $this->render('trainingType/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("show/{trainingType}", name="show_training_type")
     */
    public function show(TrainingType $trainingType, UserInterface $user)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $tTP = $trainingType->getActiveTrainingTypePersonFor($user);
            if ($tTP === null) {
                throw new AccessDeniedException('Access denied');
            }
        }

        return $this->render('trainingType/show.html.twig', [
            'trainingType' => $trainingType,
            'role' => $tTP->getRole(),
        ]);
    }

    /**
     * @Route("list", name="list_training_types")
     */
    public function showAll()
    {
        $ttRepo = $this->getDoctrine()->getManager()->getRepository(TrainingType::class);
        if ($this->isGranted('ROLE_ADMIN')) {
            $entities = $ttRepo->findAll();
        } else {
            $entities = $ttRepo->findAllForUser($this->getUser());
        }

        return $this->render('trainingType/list.html.twig', [
            'trainingTypes' => $entities,
        ]);
    }
}
