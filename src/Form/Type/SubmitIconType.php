<?php

namespace Micayael\AdminLteMakerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\SubmitButtonTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitIconType extends AbstractType implements SubmitButtonTypeInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('icon');
        $resolver->setDefault('primary', true);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['icons'] = is_array($options['icon']) ? $options['icon'] : [$options['icon']];
        $view->vars['primary'] = $options['primary'];
    }

    public function getParent()
    {
        return SubmitType::class;
    }
}
