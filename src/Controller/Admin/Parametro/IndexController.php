<?php

namespace Micayael\AdminLteMakerBundle\Controller\Admin\Parametro;

use Micayael\AdminLteMakerBundle\Entity\Parametro;
use Doctrine\ORM\QueryBuilder;
use Micayael\AdminLteMakerBundle\Framework\Base\CRUD\CriteriaSearchController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_PARAMETRO_READ')")
 */
class IndexController extends CriteriaSearchController
{
    protected function getSubjectClass(): string
    {
        return Parametro::class;
    }

    protected function getTemplateName(): string
    {
        return '@MicayaelAdminLteMakerBundle/admin/parametro/index.html.twig';
    }

    protected function setOrderBy(QueryBuilder $qb): void
    {
        $qb->orderBy('p.dominio, p.orden');
    }
}
