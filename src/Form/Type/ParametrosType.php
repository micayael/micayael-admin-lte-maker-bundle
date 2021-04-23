<?php

namespace Micayael\AdminLteMakerBundle\Form\Type;

use Micayael\AdminLteMakerBundle\Service\ParametroService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametrosType extends AbstractType
{
    private $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('dominio');
        $resolver->setAllowedTypes('dominio', 'string');

        $resolver->setDefaults([
            'choice_loader' => function (Options $options) {
                $data = $this->parametroService->getParametro($options['dominio']);

                return new CallbackChoiceLoader(function () use ($data) {
                    return array_flip($data);
                });
            },
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
