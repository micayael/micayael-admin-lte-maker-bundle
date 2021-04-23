<?php

namespace Micayael\AdminLteMakerBundle\Controller\Admin\Parametro;

use Micayael\AdminLteMakerBundle\Entity\Parametro;
use Micayael\AdminLteMakerBundle\Framework\Base\CRUD\ViewerController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_PARAMETRO_READ')")
 */
class ShowController extends ViewerController
{
    protected function getSubjectClass(): string
    {
        return Parametro::class;
    }

    protected function getSubjectName(): string
    {
        return 'parametro';
    }

    protected function getTemplateName(): string
    {
        return '@MicayaelAdminLteMakerBundle/admin/parametro/show.html.twig';
    }
}
