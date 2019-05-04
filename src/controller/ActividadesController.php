<?php

namespace Drupal\mancal_cagf\controller;

use Drupal\mancal_cagf\Forms\ActividadesTabla;
use Drupal;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\mancal_cagf\Repository\CategoriasRepo;

class ActividadesController extends ControllerBase
{
    protected $formBuilder;
    protected $db;
    protected $request;

    public function __construct(FormBuilder $form_builder, Connection $con, RequestStack $request)
    {
        $this->formBuilder = $form_builder;
        $this->db = $con;
        $this->request = $request;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('form_builder'),
            $container->get('database'),
            $container->get('request_stack')
        );
    }

    public function listarActividades()
    {
        $content = [];

        $actividades_form_inst = new ActividadesTabla($this->db);
        $content['table'] = $this->formBuilder->getForm($actividades_form_inst);
        $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

        return $content;
    }

    public function detallarActividad($actividad, $js = 'nojs')
    {
        if ($actividad == 'invalid') {
            drupal_set_message(t('Actividad no valida'), 'error');
            return new RedirectResponse(Drupal::url('mancal_cagf.listarActividades'));
        }

        $categoria_mostrar = 'Ocurrió un error al mostrar la categoría.';
        $categoria_busq = CategoriasRepo::buscarCategoria($actividad->categoria);
        $categoria_mostrar = $categoria_busq['nombre'];

        $frequencia_mostrar = '';
        if (!empty($actividad->frecuencia_dias)) {
            $numero_freq = $actividad->frecuencia_dias;
            $dias_semana = [
                ['num' => 1, 'dia' => 'Domingo'],
                ['num' => 2, 'dia' => 'Lunes'],
                ['num' => 3, 'dia' => 'Martes'],
                ['num' => 4, 'dia' => 'Miércoles'],
                ['num' => 5, 'dia' => 'Jueves'],
                ['num' => 6, 'dia' => 'Viernes'],
                ['num' => 7, 'dia' => 'Sábado'],
            ];

            foreach ($dias_semana as $dia) {
                if ($dia['num'] == $numero_freq) {
                    $frequencia_mostrar = $dia['dia'];
                }
            }
        }

        $rows[] = [
            ['data' => 'Título', 'header' => TRUE],
            $actividad->titulo,
        ];

        if ($actividad->descripcion) {
            $rows[] = [
                ['data' => 'Descripción', 'header' => TRUE],
                $actividad->descripcion,
            ];
        }



        if ($actividad->encargado) {
            $rows[] = [
                ['data' => 'Encargado', 'header' => TRUE],
                $actividad->encargado,
            ];
        }

        if ($actividad->contacto) {
            $rows[] = [
                ['data' => 'Información de contacto', 'header' => TRUE],
                $actividad->contacto,
            ];
        }

        if ($actividad->link_publicacion_fb) {
            $rows[] = [
                ['data' => 'Enlace a publicación de facebook', 'header' => TRUE],
                $actividad->link_publicacion_fb,
            ];
        }

        $rows[] = [
            ['data' => 'Categoría', 'header' => TRUE],
            $categoria_mostrar,
        ];

        $rows[] = [
            ['data' => 'Fecha de Inicio', 'header' => TRUE],
            date("d/m/Y", strtotime($actividad->inicio_fecha)),
        ];

        $rows[] = [
            ['data' => 'Fecha Final', 'header' => TRUE],
            is_null($actividad->final_fecha) ? '' : date("d/m/Y", strtotime($actividad->final_fecha)),
        ];

        $rows[] = [
            ['data' => 'Hora', 'header' => TRUE],
            $actividad->hora,
        ];


        if ($actividad->frequencia_mostrar) {
            $rows[] = [
                ['data' => 'Día específico en que se repetirá', 'header' => TRUE],
                $frequencia_mostrar,
            ];
        }

        $rows[] = [
            ['data' => 'Cancelado', 'header' => TRUE],
            ($actividad->cancelado == 0) ? 'No' : 'Si',
        ];


        if ($actividad->cancelado == 1) {
            $rows[] = [
                ['data' => 'Motivo de Cancelación', 'header' => TRUE],
                $actividad->motivo_cancelacion,
            ];
        }

        $content['details'] = [
            '#type' => 'table',
            '#rows' => $rows,
            '#attributes' => ['class' => ['actividad-detalle']],
        ];

        $content['edit'] = [
            '#type' => 'link',
            '#title' => 'Editar',
            '#attributes' => ['class' => ['button button--primary']],
            '#url' => Url::fromRoute('mancal_cagf.editarActividad', ['actividad' => $actividad->id_actividad]),
        ];

        $content['delete'] = [
            '#type' => 'link',
            '#title' => 'Eliminar',
            '#attributes' => ['class' => ['button']],
            '#url' => Url::fromRoute('mancal_cagf.eliminarActividad', ['id' => $actividad->id_actividad]),
        ];

        if ($js == 'ajax') {
            $modal_title = t('Actividad #@id_actividad', ['@id_actividad' => $actividad->id_actividad]);
            $options = [
                'dialogClass' => 'popup-dialog-class',
                'width' => '70%',
                'height' => '80%',
            ];

            $response = new AjaxResponse();
            $response->addCommand(new OpenModalDialogCommand($modal_title, $content, $options));

            return $response;
        } else {
            return $content;
        }
    }
}
