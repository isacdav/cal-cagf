<?php

namespace Drupal\mancal_cagf\Forms;

use Drupal;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;

class ActividadesTabla implements FormInterface
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
        return 'actividades_tabla_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $header = [
            ['data' => t('Id'), 'field' => 'a.id_actividad'],
            ['data' => t('Título'), 'field' => 'a.titulo'],
            ['data' => t('Descripción'), 'field' => 'a.descripcion'],
            ['data' => t('Encargado'), 'field' => 'a.encargado'],
            ['data' => t('Fecha de Inicio'), 'field' => 'a.inicio_fecha'],
            ['data' => t('Fecha de Fin'), 'field' => 'a.final_fecha'],
            ['data' => t('Hora'), 'field' => 'a.inicio_hora'],
            ['data' => t('Cancelado'), 'field' => 'a.cancelado'],
            'actions' => 'Operaciones',
        ];

        $query = $this->db->select('actividades', 'a')
            ->fields('a')
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

            $view_url = Url::fromRoute('mancal_cagf.detallarActividad', ['actividad' => $row->id_actividad, 'js' => 'nojs']);

            $ajax_view_url = Url::fromRoute('mancal_cagf.detallarActividad', ['actividad' => $row->id_actividad, 'js' => 'ajax'], $ajax_link_attributes);

            $ajax_view_link = Drupal::l($row->titulo, $ajax_view_url);

            $drop_button = [
                '#type' => 'dropbutton',
                '#links' => [
                    'view' => [
                        'title' => t('Ver'),
                        'url' => $view_url,
                    ],
                    'edit' => [
                        'title' => t('Editar'),
                        'url' => Url::fromRoute('mancal_cagf.editarActividad', ['actividad' => $row->id_actividad]),
                    ],
                    'delete' => [
                        'title' => t('Eliminar'),
                        'url' => Url::fromRoute('mancal_cagf.eliminarActividad', ['id' => $row->id_actividad]),
                    ],
                ],
            ];

            $rows[$row->id_actividad] = [
                [sprintf("%04s", $row->id_actividad)],
                [$ajax_view_link],
                [$row->descripcion],
                [$row->encargado],
                [date("d/m/Y", strtotime($row->inicio_fecha))],
                [is_null($row->final_fecha) ? '' : date("d/m/Y", strtotime($row->final_fecha))],
                [$row->hora],
                [($row->cancelado == 0) ? 'No' : 'Si'],
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
                'id' => 'actividades-tabla',
            ],
        ];

        return $form;
    }


    public function validateForm(array &$form, FormStateInterface $form_state)
    { }


    public function submitForm(array &$form, FormStateInterface $form_state)
    { }
}