<?php

namespace Drupal\mancal_cagf\controller;

use Drupal\mancal_cagf\Forms\TiposTabla;
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

class TiposController extends ControllerBase
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

    public function listarTipos()
    {
        $content = [];

        $tipos_form_inst = new TiposTabla($this->db);
        $content['table'] = $this->formBuilder->getForm($tipos_form_inst);
        $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

        return $content;
    }

    public function detallarTipo($tipo, $js = 'nojs')
    {
        if ($tipo == 'invalid') {
            drupal_set_message(t('Tipo de actividad no válido'), 'error');
            return new RedirectResponse(Drupal::url('mancal_cagf.listarTipos'));
        }

        $rows = [
            [
                ['data' => 'Nombre', 'header' => TRUE],
                $tipo->nombre,
            ],
            [
                ['data' => 'Color', 'header' => TRUE],
                $tipo->color,
            ],
        ];

        $content['details'] = [
            '#type' => 'table',
            '#rows' => $rows,
            '#attributes' => ['class' => ['tipo-detalle']],
        ];

        $content['edit'] = [
            '#type' => 'link',
            '#title' => 'Editar',
            '#attributes' => ['class' => ['button button--primary']],
            '#url' => Url::fromRoute('mancal_cagf.editarTipo', ['tipo' => $tipo->id_tipo]),
        ];

        $content['delete'] = [
            '#type' => 'link',
            '#title' => 'Eliminar',
            '#attributes' => ['class' => ['button']],
            '#url' => Url::fromRoute('mancal_cagf.eliminarTipo', ['id' => $tipo->id_tipo]),
        ];

        if ($js == 'ajax') {
            $modal_title = t('Tipo #@id_tipo', ['@id_tipo' => $tipo->id_tipo]);
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
