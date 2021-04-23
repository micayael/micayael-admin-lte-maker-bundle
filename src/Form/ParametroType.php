<?php

namespace Micayael\AdminLteMakerBundle\Form;

use Micayael\AdminLteMakerBundle\Entity\Parametro;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dominio')
            ->add('tipo', ChoiceType::class, [
                'placeholder' => '--',
                'choices' => Parametro::TIPOS,
            ])
            ->add('codigo')
            ->add('valor')
            ->add('orden')
            ->add('revision', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parametro::class,
        ]);
    }
}
