<?php

namespace Micayael\AdminLteMakerBundle\Framework\Base\CRUD;

use Micayael\AdminLteMakerBundle\Exception\RedirectException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    protected function throw404IfNotFound($objectOrArray)
    {
        if (!$objectOrArray || empty($objectOrArray)) {
            throw $this->createNotFoundException();
        }
    }

    protected function createRedirectException(Request $request, TranslatorInterface $translator): RedirectException
    {
        $routeData = $this->getOptimisticLockRedirectionRouteData($request);

        $routeName = $routeData['route'];
        $routeParams = $routeData['route_params'];

        $message = $translator->trans('crud.msg.optimistic_lock_detected', [], 'MicayaelAdminLteMakerBundle');

        return new RedirectException($message, $routeName, $routeParams);
    }

    /**
     * Agrega un mensaje flash de tipo success.
     *
     * @param $message
     */
    protected function addSuccessMessage($message): void
    {
        $this->addFlash('success', $message);
    }

    /**
     * Agrega un mensaje flash de tipo warning.
     *
     * @param $message
     */
    protected function addWarningMessage($message): void
    {
        $this->addFlash('warning', $message);
    }

    /**
     * Agrega un mensaje flash de tipo danger.
     *
     * @param $message
     */
    protected function addDangerMessage($message): void
    {
        $this->addFlash('danger', $message);
    }

    /**
     * Agrega un mensaje flash de tipo info.
     *
     * @param $message
     */
    protected function addInfoMessage($message): void
    {
        $this->addFlash('info', $message);
    }
}
