<?php

namespace App\Controller;

use App\Entity\Attendance;
use App\Entity\Training;
use App\Entity\User;
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
     * @Route("public/get/person/{mail}/{hash}", name="find_person_by_mail")
     */
    public function findByMail($mail, $hash, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);;
        $users = $em->getRepository(User::class)->findByEmail($mail);
        $unregisteredPersons = [];
        $registeredPersons = [];
        foreach ($users as $user) {
            foreach ($user->getPersons() as $person) {
                if (!$training || $training->getAttendanceForPerson($person) === null) {
                    $unregisteredPersons[] = $person;
                } else {
                    $registeredPersons[] = $person;
                }
            }
        }

        return new JsonResponse([
            'unregistered' => $serializer->normalize($unregisteredPersons, 'json', ['groups' => 'public']),
            'registered' => $serializer->normalize($registeredPersons, 'json', ['groups' => 'public'])
        ]);
    }

    /**
     * @Route("public/register/person", name="register_person")
     */
    public function registerUser(Request $request, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $request->get('email');
        $user = $em->getRepository(User::class)->findOneByEmail($email);
        if ($user === null) {
            $user = new User();
            $user->setEmail($email);
        }
        $person = new Person();
        $person->setUser($user);
        $person->setFirstName($request->get('firstName'));
        $person->setFamilyName($request->get('familyName'));
        $person->setBirthdate(new \DateTime($request->get('birthdate')));
        $person->setCity($request->get('city'));
        $person->setZipCode($request->get('zipCode'));
        $person->setStreet($request->get('street'));
        $person->setStreetNo($request->get('streetNo'));
        $person->setPhone($request->get('phone'));

        $em->persist($user);
        $em->persist($person);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'person' => $serializer->normalize($person, 'json', ['groups' => 'public']),
        ]);
    }

    /**
     * @Route("public/subscribe/{hash}/{person}", name="subscribe_person_for_training")
     */
    public function subscribeUserForTraining(Request $request, Person $person, $hash, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);
        $attendance = (new Attendance())
            ->setTraining($training)
            ->setPerson($person)
            ->setEnlistingIp($request->getClientIp())
            ->setEnlistingTimestamp(new \DateTime());
        $em->persist($attendance);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'person' => $serializer->normalize($person, 'json', ['groups' => 'public']),
            'training' => $serializer->normalize($training, 'json', ['groups' => 'public']),
        ]);
    }

    /**
     * @Route("public/unsubscribe/{hash}/{person}", name="unsubscribe_person_for_training")
     */
    public function unsubscribeUserForTraining(Person $person, $hash, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);
        $attendance = $training->getAttendanceForPerson($person);
        if ($attendance === null) {
            throw $this->createNotFoundException('Attendance not found');
        }
        if ($attendance->getConfirmationTimestamp() !== null) {
            throw new \Exception('Cannot delete attendance that has been confirmed already');
        }
        $em->remove($attendance);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'person' => $serializer->normalize($person, 'json', ['groups' => 'public']),
            'training' => $serializer->normalize($training, 'json', ['groups' => 'public']),
        ]);
    }
}
