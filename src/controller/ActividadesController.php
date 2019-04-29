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
use Drupal\mancal_cagf\Repository\TiposRepo;

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

    $tipos_de_actividad_bd = TiposRepo::listarTodos();

    $tipo_mostrar = 'El tipo de actividad de esta actividad no se encuentra. Se debe asignar uno nuevo.';
    foreach ($tipos_de_actividad_bd as $tipo) {
      if ($tipo->id_tipo == $actividad->tipo_actividad) {
        $tipo_mostrar = $tipo->nombre;
      }
    }

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

    $rows = [
      [
        ['data' => 'Título', 'header' => TRUE],
        $actividad->titulo,
      ],
      [
        ['data' => 'Descripción', 'header' => TRUE],
        $actividad->descripcion,
      ],
      [
        ['data' => 'Encargado', 'header' => TRUE],
        $actividad->encargado,
      ],
      [
        ['data' => 'Tipo de Actividad', 'header' => TRUE],
        $tipo_mostrar,
      ],
      [
        ['data' => 'Inicio', 'header' => TRUE],
        date("d/m/Y", strtotime($actividad->inicio_fecha)),
      ],
      [
        ['data' => 'Fin', 'header' => TRUE],
        is_null($actividad->final_fecha) ? '' : date("d/m/Y", strtotime($actividad->final_fecha)),
      ],
      [
        ['data' => 'Hora', 'header' => TRUE],
        $actividad->hora,
      ],
      [
        ['data' => 'Día específico en que se repetirá', 'header' => TRUE],
        $frequencia_mostrar,
      ],
      [
        ['data' => 'Cancelado', 'header' => TRUE],
        ($actividad->cancelado == 0) ? 'No' : 'Si',
      ],
    ];

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
