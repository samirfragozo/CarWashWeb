<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Spatie\Menu\Laravel\Html;
use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu;

class Base
{
    private $menu;

    /**
     * Create a controller instance.
     *
     */
    public function __construct()
    {
        $this->menu = config('menu');
    }

    /**
     * Return the HTML for the bread crumbs.
     *
     * @return Menu
     */
    public function breadCrumbs()
    {
        $menu = Menu::new()
            ->addClass('m-subheader__breadcrumbs m-nav m-nav--inline')
            ->add(
                Link::toRoute('home', '<i class="m-nav__link-icon la la-home"></i>')
                    ->addClass('m-nav__link m-nav__link--icon')
                    ->addParentClass('m-nav__item m-nav__item--home')
            )
        ;

        foreach ($this->crumbs($this->menu) as $item) {
            $menu
                ->add(
                    Html::raw('>')
                        ->addParentClass('m-nav__separator')
                )
                ->add(
                    Link::to($item['link'], '<span class="m-nav__link-text">'. $item['text'] . '</span>')
                        ->addClass('m-nav__link')
                        ->addParentClass('m-nav__item')
                )
            ;
        }

        return $menu;
    }

    /**
     * Return a array with the bread crumbs.
     *
     * @param array $options
     * @return array
     */
    private function crumbs(array $options)
    {
        foreach ($options as $option) {
            if (!isset($option['submenu'])) {
                $route = isset($option['route']) ? $option['route'] : $option['crud'] . '.index';
                if (Route::currentRouteName() == $route) {
                    if ($route !== 'home') {
                        return [
                            [
                                'link' => \route($route),
                                'text' => __('app.titles.' . $option['crud']),
                            ]
                        ];
                    }
                }
            } else {
                $items = $this->crumbs($option['submenu']);

                if (!empty($items)) {
                    array_unshift($items, [
                        'link' => '',
                        'text' => __('app.menu.' . $option['name']),
                    ]);

                    return $items;
                }
            }
        }

        return [];
    }

    /**
     * Return the HTML for the submenus.
     *
     * @param array $options
     * @return Menu
     */
    private function items(array $options)
    {
        return Menu::build($options['submenu'], function ($menu, $option) {
            if (!isset($option['submenu'])) {
                $menu->routeIfCan($option['crud'] . '.show', isset($option['route']) ? $option['route'] : $option['crud'] . '.index',
                    '<i class="m-menu__link-bullet m-menu__link-bullet--dot">' .
                    '<span></span>' .
                    '</i>' .
                    '<span class="m-menu__link-text">' . __('app.titles.' . $option['crud']) . '</span>'
                );
            } else {
                $menu->add(
                    Link::to('#',
                        '<i class="m-menu__link-bullet m-menu__link-bullet--dot">' .
                        '<span></span>' .
                        '</i>' .
                        '<span class="m-menu__link-text">' . __('app.titles.' . $option['name']) . '</span>' .
                        '<i class="m-menu__ver-arrow la la-angle-right"></i>'
                    )
                        ->addParentClass('m-menu__item m-menu__item--submenu')
                        ->addClass('m-menu__link m-menu__toggle')
                        ->append($this->items($option))
                        ->setParentAttributes(['aria-haspopup' =>'true', 'm-menu-submenu-toggle' => 'hover'])
                );
            }
        })
            ->setActive(url()->current())
            ->addClass('m-menu__subnav')
            ->append('</div>')
            ->each(function (Link $link) {
                $link->addClass('m-menu__link')
                    ->addParentClass('m-menu__item')
                    ->setParentAttribute('aria-haspopup', 'true')
                ;
            })
            ->prepend(
                '<div class="m-menu__submenu">' .
                '<span class="m-menu__arrow"></span>'
            )
            ->setActiveClass('m-menu__item--active')
            ;
    }

    /**
     * Return the HTML for the custom massive.
     *
     * @param array $options
     * @return Menu
     */
    public function massive(array $options)
    {
        return Menu::build($options, function ($menu, $option) {
            $menu->add(
                Link::to('#',
                    '<i class="m-nav__link-icon ' . $option['icon'] . '"></i>' .
                    '<span class="m-nav__link-text">' . $option['text'] . '</span>'
                )
                    ->addClass('m-nav__link')
                    ->addParentClass('m-nav__item')
                    ->setAttribute('onclick', 'openMassive(changeStatus, 1)')
            );
        })
            ->addClass('m-nav')
            ;
    }

    /**
     * Return the HTML for the menu.
     *
     * @return Menu
     */
    public function menu()
    {
        return Menu::build($this->menu , function ($menu, $option) {
            if (!isset($option['submenu'])) {
                $menu->routeIfCan($option['crud'] . '.show', isset($option['route']) ? $option['route'] : $option['crud'] . '.index',
                    '<i class="m-menu__link-icon ' . $option['icon'] . '"></i>' .
                    '<span class="m-menu__link-title">' .
                    '<span class="m-menu__link-wrap">' .
                    '<span class="m-menu__link-text">' . __('app.titles.' . $option['crud']) . '</span>' .
                    '</span>' .
                    '</span>'
                );
            } else {
                $menu->add(
                    Link::to('#',
                        '<i class="m-menu__link-icon ' . $option['icon'] . '"></i>' .
                        '<span class="m-menu__link-text">' . __('app.titles.' . $option['name']) . '</span>' .
                        '<i class="m-menu__ver-arrow la la-angle-right"></i>'

                    )
                        ->addParentClass('m-menu__item m-menu__item--submenu')
                        ->addClass('m-menu__toggle')
                        ->append($this->items($option))
                        ->setParentAttribute('m-menu-submenu-toggle', 'hover')
                );
            }
        })
            ->addClass('m-menu__nav m-menu__nav--dropdown-submenu-arrow')
            ->append('</div></div>')
            ->each(function (Link $link) {
                $link->addClass('m-menu__link')
                    ->addParentClass('m-menu__item')
                    ->setParentAttribute('aria-haspopup', 'true');
            })
            ->setActive(url()->current())
            ->setActiveClass('m-menu__item--active')
        ;
    }

    /**
     * Return a list of permissions in the menu.
     *
     * @param array $options
     * @return array
     */
    public function permissions(array $options = null)
    {
        $options = is_null($options) ? $this->menu : $options;

        $permissions = [];

        foreach ($options as $option) {
            if (!isset($option['submenu'])) {
                array_push($permissions, $option['crud']);
            } else {
                $permissions = array_merge($permissions, $this->permissions($option['submenu']));
            }
        }

        return $permissions;
    }

    /**
     * Dynamic instance of the models.
     *
     * @param string $model
     * @return Model
     */
    public static function dynamicInstanceModel(string $model)
    {
        $model = '\\App\\' . str_replace('_', '', ucwords(str_replace('_id', '', $model), '_'));
        return  new $model;
    }

    /**
     * Return data for the specified select.
     *
     * @param string $model
     * @param string $where
     * @param string $value
     * @return int
     */
    public static function findBy(string $model, string $where, $value)
    {
        $entity = Base::dynamicInstanceModel($model);
        $entity = $entity->where($where, $value)->first();
        return is_null($entity) ? -1 : $entity->id;
    }

    /**
     * Return data for the specified select.
     *
     * @param string $model
     * @param string|null $field
     * @return \Illuminate\Http\Response
     */
    public static function select(string $model, string $field = null)
    {
        $entity = Base::dynamicInstanceModel($model);
        return is_null($field) ? $entity->select() : $entity->select($field);
    }
}