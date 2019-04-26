<?php

namespace Drupal\mancal_cagf\Forms;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Url;
use Drupal\mancal_cagf\Repository\TiposRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TiposForm extends FormBase
{
    public function getFormId()
    {
        return 'tipos_agregar';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $tipo = NULL)
    {
        if ($tipo) {
            if ($tipo == 'invalid') {
                drupal_set_message(t('Tipo de actividad erróneo'), 'error');
                return new RedirectResponse(Drupal::url('mancal_cagf.listarTipos'));
            }
            $form['id_tipo'] = [
                '#type' => 'hidden',
                '#value' => $tipo->id_tipo,
            ];
        }

        $form['#attributes']['novalidate'] = '';

        $form['general'] = [
            '#type' => 'details',
            "#title" => "Detalles del Tipo de Actividad",
            '#open' => TRUE,
        ];

        $form['general']['nombre'] = [
            '#type' => 'textfield',
            '#title' => t('Nombre'),
            '#required' => TRUE,
            '#default_value' => ($tipo) ? $tipo->nombre : '',
        ];

        $form['general']['color'] = [
            '#type' => 'textfield',
            '#title' => t('Color'),
            '#required' => TRUE,
            '#default_value' => ($tipo) ? $tipo->color : '',
        ];

        $form['actions'] = ['#type' => 'actions'];
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Guardar',
        ];

        $form['actions']['cancel'] = [
            '#type' => 'link',
            '#title' => 'Cancelar',
            '#attributes' => ['class' => ['button', 'button--primary']],
            '#url' => Url::fromRoute('mancal_cagf.listarTipos'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        /*$color = $form_state->getValue('color');
        if (!preg_match('//', $color)) {
            $form_state->setErrorByName('color', $this->t('El color brindado no está el formato solicitado'));
        }*/
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $id = $form_state->getValue('id_tipo');
        $nombre = $form_state->getValue('nombre');
        $color = $form_state->getValue('color');

        $fields = [
            'nombre' => SafeMarkup::checkPlain($form_state->getValue('nombre')),
            'color' => SafeMarkup::checkPlain($form_state->getValue('color')),
        ];

        if (!empty($id) && TiposRepo::existe($id)) {
            TiposRepo::actualizar($id, $fields);
            $message = 'Tipo de actividad ' . $fields['nombre'] . ' actualizado';
        } else {
            TiposRepo::agregar($fields);
            $message = 'Nuevo tipo de actividad ' . $fields['nombre'] . ' guardado';
        }

        drupal_set_message($message);
        $form_state->setRedirect('mancal_cagf.listarTipos');
    }
}
