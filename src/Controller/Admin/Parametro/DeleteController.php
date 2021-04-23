<?php

namespace Micayael\AdminLteMakerBundle\Controller\Admin\Parametro;

use Micayael\AdminLteMakerBundle\Entity\Parametro;
use Micayael\AdminLteMakerBundle\Framework\Base\CRUD\DestructorController;
use Micayael\AdminLteMakerBundle\Service\ParametroService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_PARAMETRO_DELETE')")
 */
class DeleteController extends DestructorController
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

    protected function getSubjectName(): string
    {
        return 'parametro';
    }

    protected function getTargetRouteName(): string
    {
        return 'parametro_index';
    }

    protected function getTemplateName(): string
    {
        return '@MicayaelAdminLteMakerBundle/admin/parametro/delete.html.twig';
    }

    protected function postRemove($subject): void
    {
        $this->parametroService->clear();
    }
}
