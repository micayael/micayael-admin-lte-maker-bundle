<?php

namespace Micayael\AdminLteMakerBundle\Twig;

use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MicayaelAdminLteMakerExtension extends AbstractExtension
{
    private $router;

    private $translator;

    private $authorization;

    private $actions = [
        'index' => [
            'name' => 'crud.action.index',
            'icon' => 'fas fa-list',
            'class' => 'default',
        ],
        'new' => [
            'name' => 'crud.action.new',
            'icon' => 'fa fa-plus-circle',
            'class' => 'default',
        ],
        'show' => [
            'name' => 'crud.action.show',
            'icon' => 'fas fa-eye',
            'class' => 'default',
        ],
        'edit' => [
            'name' => 'crud.action.edit',
            'icon' => 'fas fa-edit',
            'class' => 'warning',
        ],
        'delete' => [
            'name' => 'crud.action.delete',
            'icon' => 'fas fa-trash-alt',
            'class' => 'danger',
        ],
    ];

    public function __construct(RouterInterface $router, TranslatorInterface $translator, AuthorizationCheckerInterface $authorization)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->authorization = $authorization;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('create_link', [$this, 'createLinkFunction'], ['is_safe' => ['html']]),
            new TwigFunction('create_button', [$this, 'createButtonFunction'], ['is_safe' => ['html']]),
            new TwigFunction('create_extra_link', [$this, 'createExtraLinkFunction'], ['is_safe' => ['html']]),
            new TwigFunction('create_extra_link_button', [$this, 'createExtraLinkButtonFunction'], ['is_safe' => ['html']]),
            new TwigFunction('create_extra_button', [$this, 'createExtraButtonFunction'], ['is_safe' => ['html']]),

            new TwigFunction('boolean_value', [$this, 'booleanValueFunction'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('json_to_ul', [$this, 'jsonToUlFilter'], ['is_safe' => ['html']]),
            new TwigFilter('json_to_label', [$this, 'jsonToLabelFilter'], ['is_safe' => ['html']]),
            new TwigFilter('objects_to_ul', [$this, 'objectsToUlFilter'], ['is_safe' => ['html']]),
        ];
    }

    public function createLinkFunction(string $type, string $route, ?array $routeArgs = [], ?string $role = null)
    {
        if (!$role || ($role && ($this->authorization->isGranted('ROLE_SUPER_ADMIN') || $this->authorization->isGranted($role)))) {
            $ret = sprintf(
                '<a href="%s"><i class="%s" aria-hidden="true"></i> %s</a>',
                $this->router->generate($route, $routeArgs),
                $this->actions[$type]['icon'],
                $this->translator->trans($this->actions[$type]['name'], [], 'MicayaelAdminLteMakerBundle')
            );

            return $ret;
        }
    }

    public function createButtonFunction(string $type, string $route, ?array $routeArgs = [], ?string $role = null)
    {
        if (!$role || ($role && ($this->authorization->isGranted('ROLE_SUPER_ADMIN') || $this->authorization->isGranted($role)))) {
            $ret = sprintf(
                '<a href="%s" class="btn btn-%s"><i class="%s" aria-hidden="true"></i> %s</a>',
                $this->router->generate($route, $routeArgs),
                $this->actions[$type]['class'],
                $this->actions[$type]['icon'],
                $this->translator->trans($this->actions[$type]['name'], [], 'MicayaelAdminLteMakerBundle')
            );

            return $ret;
        }
    }

    public function createExtraButtonFunction(string $name, ?string $role = null, ?string $class = null, ?string $icon = null)
    {
        if (!$role || ($role && ($this->authorization->isGranted('ROLE_SUPER_ADMIN') || $this->authorization->isGranted($role)))) {
            $ret = sprintf(
                '<button class="btn btn-%s"><i class="%s"></i> %s</button>',
                $class,
                $icon,
                $this->translator->trans($name, [], 'MicayaelAdminLteMakerBundle')
            );

            return $ret;
        }
    }

    public function createExtraLinkFunction(string $name, string $route, ?array $routeArgs = [], ?string $role = null, ?string $icon = null)
    {
        if (!$role || ($role && ($this->authorization->isGranted('ROLE_SUPER_ADMIN') || $this->authorization->isGranted($role)))) {
            $ret = sprintf(
                '<a href="%s"s><i class="%s"></i> %s</a>',
                $this->router->generate($route, $routeArgs),
                $icon,
                $this->translator->trans($name, [], 'MicayaelAdminLteMakerBundle')
            );

            return $ret;
        }
    }

    public function createExtraLinkButtonFunction(string $name, string $route, ?array $routeArgs = [], ?string $role = null, ?string $class = null, ?string $icon = null)
    {
        if (!$role || ($role && ($this->authorization->isGranted('ROLE_SUPER_ADMIN') || $this->authorization->isGranted($role)))) {
            $ret = sprintf(
                '<a href="%s" class="btn btn-%s"><i class="%s"></i> %s</a>',
                $this->router->generate($route, $routeArgs),
                $class,
                $icon,
                $this->translator->trans($name, [], 'MicayaelAdminLteMakerBundle')
            );

            return $ret;
        }
    }

    public function booleanValueFunction(bool $bool, bool $inverted = false)
    {
        $trueClass = 'success';
        $falseClass = 'danger';

        if ($inverted) {
            $trueClass = 'danger';
            $falseClass = 'success';
        }

        if ($bool) {
            return '<span class="label label-'.$trueClass.'">Si</span>';
        } else {
            return '<span class="label label-'.$falseClass.'">No</span>';
        }
    }

    /**
     * @param PersistentCollection $items
     *
     * @return bool|string
     */
    public function jsonToUlFilter($items)
    {
        $ret = '<ul>';

        foreach ($items as $item) {
            $ret .= '<li>'.$item.'</li>';
        }

        $ret .= '</ul>';

        return $ret;
    }

    /**
     * @param PersistentCollection $items
     *
     * @return bool|string
     */
    public function jsonToLabelFilter($items)
    {
        $ret = '';

        foreach ($items as $item) {
            $ret .= '<span class="label label-default">'.$item.'</span> ';
        }

        return $ret;
    }

    /**
     * @param PersistentCollection $items
     *
     * @return bool|string
     */
    public function objectsToUlFilter($items)
    {
        $ret = '<ul>';

        foreach ($items as $item) {
            $ret .= '<li>'.$item.'</li>';
        }

        $ret .= '</ul>';

        return $ret;
    }
}
