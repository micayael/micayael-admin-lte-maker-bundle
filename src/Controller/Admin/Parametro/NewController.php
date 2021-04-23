<?php

namespace Micayael\AdminLteMakerBundle\Controller\Admin\Parametro;

use Micayael\AdminLteMakerBundle\Entity\Parametro;
use Micayael\AdminLteMakerBundle\Form\ParametroType;
use Micayael\AdminLteMakerBundle\Framework\Base\CRUD\CreatorController;
use Micayael\AdminLteMakerBundle\Service\ParametroService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_PARAMETRO_CREATE')")
 */
class NewController extends CreatorController
{
    private $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    protected function createSubject(Request $request)
    {
        return new Parametro();
    }

    protected function getSubjectName(): string
    {
        return 'parametro';
    }

    protected function getSubjectFormTypeClass(): string
    {
        return ParametroType::class;
    }

    protected function getTargetRouteName(): string
    {
        return 'parametro';
    }

    protected function getTemplateName(): string
    {
        return '@MicayaelAdminLteMakerBundle/admin/parametro/new.html.twig';
    }

    protected function postPersist($subject): void
    {
        $this->parametroService->clear();
    }
}
