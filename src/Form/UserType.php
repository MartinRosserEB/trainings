<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class)
            ->add('persons', CollectionType::class, [
                'entry_type' => PersonType::class,
                'by_reference' => false,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $this->addRoles($builder);
        }
    }

    private function addRoles(FormBuilderInterface $builder)
    {
        $roleChoiceOptions = [
            'choices' => [
                'Benutzer' => 'ROLE_USER',
                'Administrator' => 'ROLE_ADMIN',
            ],
            'required' => false,
            'multiple' => true,
        ];
        if (count($builder->getData()->getRoles()) === 0) {
            $roleChoiceOptions['data'] = ['Benutzer' => 'ROLE_USER'];
        }
        $builder->add('roles', ChoiceType::class, $roleChoiceOptions);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
