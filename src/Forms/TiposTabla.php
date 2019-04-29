<?php

namespace Drupal\mancal_cagf\Forms;

use Drupal;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Database\Connection;

class TiposTabla implements FormInterface
{

    protected $db;

    public function __construct(Connection $con)
    {
        $this->db = $con;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('database')
        );
    }

    public function getFormId()
    {
        return 'tipos_tabla_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        global $base_url;

        $header = [
            ['data' => t('Id'), 'field' => 't.id_tipo'],
            ['data' => t('Nombre'), 'field' => 't.nombre'],
            ['data' => t('Color'), 'field' => 't.color'],
            'actions' => 'Operaciones',
        ];


        $query = $this->db->select('tipos_actividades', 't')
            ->fields('t')
            ->extend('Drupal\Core\Database\Query\TableSortExtender');

        $query->orderByHeader($header);

        $results = $query->execute();
        $rows = [];
        foreach ($results as $row) {
            $ajax_link_attributes = [
                'attributes' => [
                    'class' => 'use-ajax',
                    'data-dialog-type' => 'modal',
                    'data-dialog-options' => ['width' => 700, 'height' => 400],
                ],
            ];

            $view_url = Url::fromRoute('mancal_cagf.detallarTipo', ['tipo' => $row->id_tipo, 'js' => 'nojs']);

            $ajax_view_url = Url::fromRoute('mancal_cagf.detallarTipo', ['tipo' => $row->id_tipo, 'js' => 'ajax'], $ajax_link_attributes);

            $ajax_view_link = Drupal::l($row->nombre, $ajax_view_url);


            $color_link = $row->color;
            $link_color_view = Link::fromTextAndUrl($color_link, Url::fromUri('https://www.color-hex.com/color/' . ltrim($color_link, '#')));

            $drop_button = [
                '#type' => 'dropbutton',
                '#links' => [
                    'view' => [
                        'title' => t('Ver'),
                        'url' => $view_url,
                    ],
                    'edit' => [
                        'title' => t('Editar'),
                        'url' => Url::fromRoute('mancal_cagf.editarTipo', ['tipo' => $row->id_tipo]),
                    ],
                    'delete' => [
                        'title' => t('Eliminar'),
                        'url' => Url::fromRoute('mancal_cagf.eliminarTipo', ['id' => $row->id_tipo]),
                    ],
                ],
            ];

            $rows[$row->id_tipo] = [
                [sprintf("%04s", $row->id_tipo)],
                [$ajax_view_link],
                [$link_color_view],
                'actions' => [
                    'data' => $drop_button,
                ],
            ];
        }

        $form['table'] = [
            '#type' => 'tableselect',
            '#header' => $header,
            '#options' => $rows,
            '#attributes' => [
                'id' => 'tipos-tabla',
            ],
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    { }


    public function submitForm(array &$form, FormStateInterface $form_state)
    { }
}
