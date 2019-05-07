<?php

namespace Drupal\mancal_cagf\controller;

use Drupal;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\mancal_cagf\Repository\CategoriasRepo;

class CategoriasController extends ControllerBase
{
    public function verCategorias()
    {
        $categorias_lista = CategoriasRepo::getCategorias();

        if (!$categorias_lista) {
            drupal_set_message(t('No se encontraron categorías'), 'error');
            return new RedirectResponse(Drupal::url('mancal_cagf.listarActividades'));
        }

        $header = [
            ['data' => t('Nombre'), 'field' => 'c.nombre'],
            ['data' => t('Color'), 'field' => 'c.color'],
        ];

        $rows = [];

        foreach ($categorias_lista as $cat) {
            $color_link = $cat['color'];
            $link_color_view = Link::fromTextAndUrl($color_link, Url::fromUri('https://www.color-hex.com/color/' . ltrim($color_link, '#')));

            $rows[] = [
                $cat['nombre'],
                $link_color_view,
            ];
        }

        $content['table'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => [
                'id' => 'categorias-tabla',
            ],
        ];

        return $content;
    }
}
