<?php

namespace App\Form;

use App\Entity\TrainingType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author mrosser
 */
class TrainingTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $trainingTypeUsers = [];
        foreach ($entity->getActiveTrainingTypeUsers() as $tTU) {
            $trainingTypeUsers[] = $tTU->getUser();
        }

        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('trainingTypeUsers', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'data' => $trainingTypeUsers,
                'mapped' => false,
                'required' => false
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrainingType::class,
        ]);
    }
}
