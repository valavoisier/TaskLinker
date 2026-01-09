<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('deadline')
            ->add('status', EnumType::class, [
                'class' => TaskStatus::class,
                'choice_label' => fn(TaskStatus $status) => $status->getLabel(),
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choices' => $options['project'] ? $options['project']->getEmployees() : [], // Limite les employés au projet courant               
                'choice_label' => function (Employee $employee) {
                    return $employee->getFirstname() . ' ' . $employee->getLastname();
                },
                'placeholder' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project' => null,// Ajout de l'option 'project' pour passer le projet courant
        ]);
    }
}
