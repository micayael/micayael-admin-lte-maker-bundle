<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use <?= $entity_full_class_name; ?>;
use Doctrine\ORM\QueryBuilder;
use Micayael\AdminLteMakerBundle\Framework\Base\CRUD\CriteriaSearchController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Intl\Exception\NotImplementedException;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_<?= $entity_class_name_upper; ?>_READ')")
 */
class <?= $class_name; ?> extends CriteriaSearchController
{
    protected function getSubjectClass(): string
    {
        return <?= $entity_class_name; ?>::class;
    }

    protected function getTemplateName(): string
    {
        return '<?= $templates_path; ?>/index.html.twig';
    }

    protected function setOrderBy(QueryBuilder $qb): void
    {
        // TODO: implementar este método
        //$qb->addOrderBy('<?= $sql_alias; ?>.nombre');
        throw new NotImplementedException('Este método debe ser implementado');
    }
}
