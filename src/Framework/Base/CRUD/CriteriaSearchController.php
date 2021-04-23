<?php

namespace Micayael\AdminLteMakerBundle\Framework\Base\CRUD;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class CriteriaSearchController extends BaseController implements CRUDInterface
{
    /**
     * @var ServiceEntityRepository
     */
    protected $repository;

    abstract protected function getSubjectClass(): string;

    abstract protected function getTemplateName(): string;

    abstract protected function setOrderBy(QueryBuilder $qb): void;

    public function __invoke(Request $request, PaginatorInterface $paginator): Response
    {
        $qb = $this->createQueryBuilder($request);

        $this->setOrderBy($qb);

        $query = $qb->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->getPaginatorLimit()
        );

        return $this->render($this->getTemplateName(), [
            'pagination' => $pagination,
        ]);
    }

    protected function getSubjectRepository(): ServiceEntityRepository
    {
        if (!$this->repository) {
            $this->repository = $this->getDoctrine()->getRepository($this->getSubjectClass());
        }

        return $this->repository;
    }

    protected function createQueryBuilder(Request $request): QueryBuilder
    {
        $rootAlias = strtolower(substr($this->getSubjectClass(), strrpos($this->getSubjectClass(), '\\') + 1, 1));

        return $this->getSubjectRepository()->createQueryBuilder($rootAlias);
    }

    protected function getPaginatorLimit(): int
    {
        return 10;
    }
}
