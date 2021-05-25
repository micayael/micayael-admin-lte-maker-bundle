<?php

namespace Micayael\AdminLteMakerBundle\Twig;

use Micayael\AdminLteMakerBundle\Service\ParametroService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParametroExtension extends AbstractExtension
{
    private $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_param', [$this, 'getParametro'], ['is_safe' => ['html']]),
        ];
    }

    public function getParametro(string $dominio, string $codigo = null): string
    {
        $parametro = $this->parametroService->getParametro($dominio, $codigo);

        if(!$parametro){
            return $dominio . ' (' .$codigo. ')';
        }

        return $parametro;
    }
}
