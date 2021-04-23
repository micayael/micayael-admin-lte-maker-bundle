<?php

namespace Micayael\AdminLteMakerBundle\Controller\Admin\Parametro;

use Micayael\AdminLteMakerBundle\Entity\Parametro;
use Micayael\AdminLteMakerBundle\Form\ParametroType;
use Micayael\AdminLteMakerBundle\Framework\Base\CRUD\UpdaterController;
use Micayael\AdminLteMakerBundle\Service\ParametroService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_PARAMETRO_UPDATE')")
 */
class EditController extends UpdaterController
{
    private $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    protected function getSubjectClass(): string
    {
        return Parametro::class;
    }

    protected function getSubjectFormTypeClass(): string
    {
        return ParametroType::class;
    }

    protected function getSubjectName(): string
    {
        return 'parametro';
    }

    protected function getTargetRouteName(): string
    {
        return 'parametro';
    }

    protected function getTemplateName(): string
    {
        return '@MicayaelAdminLteMakerBundle/admin/parametro/edit.html.twig';
    }

    protected function postUpdate($subject, FormInterface $form): void
    {
        $this->parametroService->clear();
    }
}
