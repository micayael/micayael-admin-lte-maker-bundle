<?php

namespace Micayael\AdminLteMakerBundle\Twig\ViewHelper\Action;

use Micayael\AdminLteMakerBundle\Entity\Parametro;
use Micayael\AdminLteMakerBundle\Framework\Base\BaseActionsViewHelper;
use Twig\TwigFunction;

class ParametroActionsViewHelper extends BaseActionsViewHelper
{
    public function getFunctions()
    {
        return [
            new TwigFunction('parametro_actions', [$this, 'create'], ['is_safe' => ['html']]),
        ];
    }

    /**
    * @param Parametro $data
    * @param array $actions
    * @return array
    */
    protected function getActions($data, array $actions)
    {
        $actions['show'] = $this->createAction($this->actions['show']['name'], 'ROLE_PARAMETRO_READ', 'parametro_show', [
        'id' => $data->getId(),
        ], $this->actions['show']['icon'], $this->actions['show']['class']);

        $actions['edit'] = $this->createAction($this->actions['edit']['name'], 'ROLE_PARAMETRO_UPDATE', 'parametro_edit', [
        'id' => $data->getId(),
        ], $this->actions['edit']['icon'], $this->actions['edit']['class'], true);

        $actions['delete'] = $this->createAction($this->actions['delete']['name'], 'ROLE_PARAMETRO_DELETE', 'parametro_delete', [
        'id' => $data->getId(),
        ], $this->actions['delete']['icon'], $this->actions['delete']['class'], true);

        return $actions;
    }
}
