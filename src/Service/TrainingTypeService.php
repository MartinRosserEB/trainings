<?php

namespace App\Service;

use App\Entity\TrainingType;
use App\Form\TrainingTypeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TrainingTypeService {
    private $formSrv;
    private $passwordEncoderSrv;
    private $em;

    public function __construct (FormFactoryInterface $formSrv, UserPasswordEncoderInterface $pwEnc, EntityManagerInterface $em)
    {
        $this->formSrv = $formSrv;
        $this->passwordEncoderSrv = $pwEnc;
        $this->em = $em;
    }

    public function handleForm(Request $request, TrainingType $trainingType)
    {
        $form = $this->formSrv->create(TrainingTypeType::class, $trainingType);
        $form->add('submit', SubmitType::class);

        $preATTPs = [];
        foreach ($trainingType->getActiveTrainingTypePersons() as $aTTP) {
            $preATTPs[] = $aTTP;
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trainingType = $form->getData();
            $now = new \DateTime();
            $curATTPs = $trainingType->getActiveTrainingTypePersons();
            foreach ($preATTPs as $preATTP) {
                if (!$curATTPs->contains($preATTP)) {
                    $preATTP->setActiveUntil($now);
                }
            }
            foreach ($curATTPs as $curATTP) {
                if ($curATTP->getActiveSince() === null) {
                    $curATTP->setActiveSince($now);
                }
            }
            $this->em->flush();
        }

        return $form;
    }
}
