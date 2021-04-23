<?php

namespace Micayael\AdminLteMakerBundle\Framework\Base\CRUD;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Micayael\AdminLteMakerBundle\Form\Type\SubmitIconType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class UpdaterController extends BaseController implements CRUDInterface
{
    protected $subject;

    /**
     * @var ServiceEntityRepository
     */
    protected $repository;

    abstract protected function getSubjectClass(): string;

    abstract protected function getSubjectName(): string;

    abstract protected function getSubjectFormTypeClass(): string;

    abstract protected function getTargetRouteName(): string;

    abstract protected function getTemplateName(): string;

    public function __invoke(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $this->subject = $this->getSubject($request);

        $this->throw404IfNotFound($this->getSubject($request));

        $form = $this->createForm($this->getSubjectFormTypeClass(), $this->getSubject($request));

        $this->addFormButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->lock($this->getSubject($request), LockMode::OPTIMISTIC, $form->getData()->getRevision());

                $this->preUpdate($this->getSubject($request), $form);

                $em->flush();

                $this->postUpdate($this->subject, $form);

                $this->addSuccessMessage($translator->trans('crud.msg.edit', [], 'MicayaelAdminLteMakerBundle'));

                $redirectTo = $this->postUpdateRedirect($request, $form, $this->subject);

                if ($redirectTo) {
                    return $redirectTo;
                }

                if ($form->getClickedButton() && 'saveAndEdit' === $form->getClickedButton()->getName()) {
                    return $this->redirectToRoute($this->getTargetRouteName().'_edit', [
                        'id' => $this->subject->getId(),
                    ]);
                } elseif ($form->getClickedButton() && 'saveAndList' === $form->getClickedButton()->getName()) {
                    return $this->redirectToRoute($this->getTargetRouteName().'_index');
                }
            } catch (OptimisticLockException $e) {
                throw $this->createRedirectException($request, $translator);
            }
        }

        return $this->render($this->getTemplateName(), [
            $this->getSubjectName() => $this->getSubject($request),
            'form' => $form->createView(),
        ]);
    }

    protected function addFormButtons(FormInterface $form): void
    {
        $form->add('saveAndEdit', SubmitIconType::class, [
            'label' => 'crud.action.save_and_edit',
            'translation_domain' => 'MicayaelAdminLteMakerBundle',
            'icon' => 'fas fa-save',
        ]);
        $form->add('saveAndList', SubmitIconType::class, [
            'label' => 'crud.action.save_and_list',
            'translation_domain' => 'MicayaelAdminLteMakerBundle',
            'icon' => ['fas fa-save', 'fas fa-list'],
            'primary' => false,
        ]);
    }

    protected function getOptimisticLockRedirectionRouteData(Request $request): array
    {
        return [
            'route' => $request->get('_route'),
            'route_params' => [
                'id' => $this->getSubject($request)->getId(),
            ],
        ];
    }

    protected function preUpdate($subject, FormInterface $form): void
    {
    }

    protected function postUpdate($subject, FormInterface $form): void
    {
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

    protected function postUpdateRedirect(Request $request, FormInterface $form, $subject): ?Response
    {
        return null;
    }
}
