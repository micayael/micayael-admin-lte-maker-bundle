<?php

namespace Micayael\AdminLteMakerBundle\Framework\Base\CRUD;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ViewerController extends BaseController implements CRUDInterface
{
    private $subject;

    /**
     * @var ServiceEntityRepository
     */
    protected $repository;

    abstract protected function getSubjectClass(): string;

    abstract protected function getSubjectName(): string;

    abstract protected function getTemplateName(): string;

    public function __invoke(Request $request): Response
    {
        $this->subject = $this->getSubject($request);

        $this->throw404IfNotFound($this->getSubject($request));

        return $this->render($this->getTemplateName(), [
            $this->getSubjectName() => $this->getSubject($request),
        ]);
    }

    protected function getSubjectRepository(): ServiceEntityRepository
    {
        if (!$this->repository) {
            $this->repository = $this->getDoctrine()->getRepository($this->getSubjectClass());
        }

        return $this->repository;
    }

    protected function getSubject(Request $request)
    {
        if (!$this->subject) {
            $this->subject = $this->getSubjectRepository()->find($request->get('id'));
        }

        return $this->subject;
    }
}
