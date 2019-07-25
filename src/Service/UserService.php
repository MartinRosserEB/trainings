<?php

namespace App\Service;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService {
    private $formSrv;
    private $passwordEncoderSrv;
    private $em;

    public function __construct (FormFactoryInterface $formSrv, UserPasswordEncoderInterface $pwEnc, EntityManagerInterface $em)
    {
        $this->formSrv = $formSrv;
        $this->passwordEncoderSrv = $pwEnc;
        $this->em = $em;
    }

    public function handleForm(Request $request, User $user)
    {
        $form = $this->formSrv->create(UserType::class, $user);
        $form->add('password', TextType::class, [
            'mapped' => false,
            'required' => false,
        ])->add('submit', SubmitType::class);

        $prePersons = [];
        foreach ($user->getPersons() as $person) {
            $prePersons[] = $person;
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $pw = $form->get('password')->getData();
            if ($pw !== null) {
                $user->setPassword($this->passwordEncoderSrv->encodePassword(
                    $user,
                    $pw
                ));
            }
            foreach ($prePersons as $prePerson) {
                if (!$user->getPersons()->contains($prePerson)) {
                    $this->em->remove($prePerson);
                }
            }
            $this->em->flush();
        }

        return $form;
    }
}
