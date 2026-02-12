<?php

namespace App\Form;

use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('entryDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['max' => (new \DateTime())->format('Y-m-d')],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => EmployeeStatus::cases(), // Utilisation de l'énumération pour les choix
                'choice_label' => fn(EmployeeStatus $choice) => $choice->getLabel(),
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
