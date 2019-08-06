<?php

namespace App\Controller;

use App\Entity\Training;
use App\Entity\Person;
use App\Form\TrainingType as TrainingTypeForm;
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
        // default values for start and end since Firefox is only partly supporting datetime-local
        $training->setStart(new \DateTime('2020-03-03T19:30'));
        $training->setEnd(new \DateTime('2020-03-03T21:00'));
        $form = $this->createForm(TrainingTypeForm::class, $training);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $training = $form->getData();
            $public = null;
            if ($form->get('public')->getData()) {
                $public = md5((new \DateTime())->format('d.m.Y H:i'));
            }
            $training->setPublic($public);
            $training->setCreator($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();

            return $this->redirectToRoute("show_training", [
                'training' => $training->getId(),
            ]);
        }

        return $this->render('training/form.html.twig', [
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
            $public = null;
            if ($form->get('public')->getData()) {
                $public = md5((new \DateTime())->format('d.m.Y H:i'));
            }
            $training->setPublic($public);
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();
        }

        return $this->render('training/form.html.twig', [
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
     * @Route("confirm/attendance", name="training_confirm_attendance")
     */
    public function confirmAttendance(Request $request, UserInterface $user, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneById($request->get('training'));
        $person = $em->getRepository(Person::class)->findOneById($request->get('person'));
        $attendance = $training->getAttendanceForPerson($person);
        if ($attendance === null) {
            throw $this->createNotFoundException('Attendance not found. Training ID: '.$request->get('training').'; Person ID: '.$request->get('person'));
        }
        $attendance->setConfirmationUser($user);
        $attendance->setConfirmationTimestamp(new \DateTime());
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'person' => $serializer->normalize($person, 'json', ['groups' => 'public']),
            'training' => $serializer->normalize($training, 'json', ['groups' => 'public']),
        ]);
    }

    /**
     * @Route("remove/attendance/confirmation", name="training_remove_attendance_confirmation")
     */
    public function removeAttendanceConfirmation(Request $request, UserInterface $user, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneById($request->get('training'));
        $person = $em->getRepository(Person::class)->findOneById($request->get('person'));
        $attendance = $training->getAttendanceForPerson($person);
        if ($attendance === null) {
            throw $this->createNotFoundException('Attendance not found. Training ID: '.$request->get('training').'; Person ID: '.$request->get('person'));
        }
        $attendance->setConfirmationUser(null);
        $attendance->setConfirmationTimestamp(null);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'person' => $serializer->normalize($person, 'json', ['groups' => 'public']),
            'training' => $serializer->normalize($training, 'json', ['groups' => 'public']),
        ]);
    }
}
