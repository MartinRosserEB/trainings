<?php

namespace App\Controller;

use App\Entity\Attendance;
use App\Entity\Training;
use App\Entity\User;
use App\Entity\Person;
use App\Form\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/public/")
 */
class PublicController extends AbstractController
{
    /**
     * @Route("training/show/{hash}/{person}/{unsubscribe}", name="show_training_by_hash", defaults={"person":null,"unsubscribe":false})
     */
    public function showByHash($hash, $person, $unsubscribe)
    {
        $em = $this->getDoctrine()->getManager();
        if ($person !== null) {
            $person = $em->getRepository(Person::class)->findOneById($person);
        }

        $training = $this->getDoctrine()->getManager()->getRepository(Training::class)->findOneByPublic($hash);

        return $this->render('public/show_training_by_hash.html.twig', [
            'training' => $training,
            'hash' => $hash,
            'person' => $person,
        ]);
    }

    /**
     * @Route("person/get/{mail}/{hash}", name="find_person_by_mail")
     */
    public function findByMail($mail, $hash, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);;
        $user = $em->getRepository(User::class)->findOneByEmail($mail);
        $unregisteredPersons = [];
        $registeredPersons = [];
        $uid = null;
        if ($user !== null) {
            foreach ($user->getPersons() as $person) {
                if (!$training || $training->getAttendanceForPerson($person) === null) {
                    $unregisteredPersons[] = $person;
                } else {
                    $registeredPersons[] = $person;
                }
            }
            $uid = $user->getId();
        }

        return new JsonResponse([
            'unregistered' => $serializer->normalize($unregisteredPersons, 'json', ['groups' => 'public']),
            'registered' => $serializer->normalize($registeredPersons, 'json', ['groups' => 'public']),
            'uid' => $uid,
        ]);
    }

    /**
     * @Route("user/create/{hash}/{email}", name="public_create_user")
     */
    public function createUser($hash, $email)
    {
        $em = $this->getDoctrine()->getManager();
        if ($em->getRepository(User::class)->findOneByEmail($email) !== null) {
            throw new \InvalidArgumentException('Email already used: '.$email);
        }
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);
        if ($training === null) {
            throw new \InvalidArgumentException('No training found with public hash: '.$hash);
        }
        $user = new User();
        $user->setEmail($email);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("register_person", [
            'hash' => $hash,
            'user' => $user->getId(),
        ]);
    }

    /**
     * @Route("person/training/register/{hash}/{user}", name="register_person")
     */
    public function registerPerson(Request $request, $hash, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $person = $form->getData();
            $user->addPerson($person);
            $attendance = new Attendance($person, $training, $request->getClientIp());
            $em->persist($attendance);
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute("show_training_by_hash", [
                'hash' => $hash,
                'person' => $person->getId(),
            ]);
        }

        return $this->render('public/register_user.html.twig', [
            'form' => $form->createView(),
            'training' => $training,
        ]);
    }

    /**
     * @Route("person/training/subscribe/{hash}/{person}", name="subscribe_person_for_training")
     */
    public function subscribeUserForTraining(Request $request, Person $person, $hash, SerializerInterface $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $training = $em->getRepository(Training::class)->findOneByPublic($hash);
        $attendance = new Attendance($person, $training, $request->getClientIp());
        $em->persist($attendance);
        $em->flush();

        return $this->redirectToRoute("show_training_by_hash", [
            'hash' => $hash,
            'person' => $person->getId(),
        ]);
    }

    /**
     * @Route("person/training/unsubscribe/{hash}/{person}", name="unsubscribe_person_for_training")
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

        return $this->redirectToRoute("show_training_by_hash", [
            'hash' => $hash,
            'person' => $person->getId(),
            'unsubscribe' => true,
        ]);
    }
}
