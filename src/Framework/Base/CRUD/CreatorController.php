<?php

namespace Micayael\AdminLteMakerBundle\Framework\Base\CRUD;

use Micayael\AdminLteMakerBundle\Form\Type\SubmitIconType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class CreatorController extends BaseController implements CRUDInterface
{
    protected $subject;

    abstract protected function createSubject(Request $request);

    abstract protected function getSubjectName(): string;

    abstract protected function getSubjectFormTypeClass(): string;

    abstract protected function getTargetRouteName(): string;

    abstract protected function getTemplateName(): string;

    public function __invoke(Request $request, TranslatorInterface $translator): Response
    {
        $this->subject = $this->createSubject($request);

        $form = $this->createForm($this->getSubjectFormTypeClass(), $this->getSubject());

        $this->addFormButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $this->prePersist($this->subject);

            $entityManager->persist($this->getSubject());
            $entityManager->flush();

            $this->postPersist($this->subject);

            $this->addSuccessMessage($translator->trans('crud.msg.new', [], 'MicayaelAdminLteMakerBundle'));

            $redirectTo = $this->postPersistRedirect($request, $form, $this->subject);

            if ($redirectTo) {
                return $redirectTo;
            }

            if ($form->getClickedButton() && 'saveAndEdit' === $form->getClickedButton()->getName()) {
                return $this->redirectToRoute($this->getTargetRouteName().'_edit', [
                    'id' => $this->subject->getId(),
                ]);
            } elseif ($form->getClickedButton() && 'saveAndList' === $form->getClickedButton()->getName()) {
                return $this->redirectToRoute($this->getTargetRouteName().'_index');
            } elseif ($form->getClickedButton() && 'saveAndAdd' === $form->getClickedButton()->getName()) {
                return $this->redirectToRoute($this->getTargetRouteName().'_new');
            }
        }

        return $this->render($this->getTemplateName(), [
            $this->getSubjectName() => $this->getSubject(),
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
        $form->add('saveAndAdd', SubmitIconType::class, [
            'label' => 'crud.action.save_and_add',
            'translation_domain' => 'MicayaelAdminLteMakerBundle',
            'icon' => ['fas fa-save', 'fas fa-plus'],
            'primary' => false,
        ]);
    }

    protected function getSubject()
    {
        return $this->subject;
    }

    protected function prePersist($subject): void
    {
    }

    protected function postPersist($subject): void
    {
    }

    protected function postPersistRedirect(Request $request, FormInterface $form, $subject): ?Response
    {
        return null;
    }
}
