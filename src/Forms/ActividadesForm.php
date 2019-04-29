<?php

namespace Drupal\mancal_cagf\Forms;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Url;
use Drupal\mancal_cagf\Repository\ActividadesRepo;
use Drupal\mancal_cagf\Repository\TiposRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ActividadesForm extends FormBase
{

    public function getFormId()
    {
        return 'actividades_agregar';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $actividad = NULL)
    {
        if ($actividad) {
            if ($actividad == 'invalid') {
                drupal_set_message(t('Actividad errónea'), 'error');
                return new RedirectResponse(Drupal::url('mancal_cagf.listarActividades'));
            }
            $form['id_actividad'] = [
                '#type' => 'hidden',
                '#value' => $actividad->id_actividad,
            ];
        }

        $tipos_de_actividad_bd = TiposRepo::listarTodos();
        if (!$tipos_de_actividad_bd) {
            drupal_set_message(t('Error, debe de haber al menos un Tipo de Actividad'), 'error');
            return new RedirectResponse(Drupal::url('mancal_cagf.listarActividades'));
        }
        $tipos_select = ['' => 'Seleccionar'];
        foreach ($tipos_de_actividad_bd as $tipo) {
            $tipos_select[$tipo->id_tipo] = $tipo->nombre;
        }

        $form['#attributes']['novalidate'] = '';

        if ($actividad) {
            $form['cancelacion'] = [
                '#type' => 'details',
                "#title" => "Cancelación",
                '#open' => TRUE,
            ];

            $form['cancelacion']['cancelado'] = [
                '#type' => 'checkbox',
                '#title' => t('Cancelado'),
                '#default_value' => ($actividad) ? $actividad->cancelado : 0,
            ];
        }

        $form['general'] = [
            '#type' => 'details',
            "#title" => "Detalles generales",
            '#open' => TRUE,
        ];

        $form['general']['titulo'] = [
            '#type' => 'textfield',
            '#title' => t('Titulo'),
            '#required' => TRUE,
            '#default_value' => ($actividad) ? $actividad->titulo : '',
        ];

        $form['general']['descripcion'] = [
            '#type' => 'textarea',
            '#title' => t('Descripción'),
            '#default_value' => ($actividad) ? $actividad->descripcion : '',
        ];

        $form['general']['encargado'] = [
            '#type' => 'textfield',
            '#title' => t('Persona a Cargo'),
            '#required' => TRUE,
            '#default_value' => ($actividad) ? $actividad->encargado : '',
        ];

        $form['general']['tipo_actividad'] = [
            '#type' => 'select',
            '#title' => t('Tipo de Atividad'),
            '#options' => $tipos_select,
            '#required' => TRUE,
            '#default_value' => ($actividad) ? $actividad->tipo_actividad : '',
        ];

        $form['fechas'] = [
            '#type' => 'details',
            "#title" => "Fechas",
            '#open' => TRUE,
        ];

        $form['fechas']['inicio_fecha'] = [
            '#type' => 'date',
            '#title' => t('Fecha de Inicio'),
            '#required' => TRUE,
            '#default_value' => ($actividad) ? $actividad->inicio_fecha : '',
        ];

        $form['fechas']['hora'] = [
            '#type' => 'textfield',
            '#title' => t('Hora'),
            '#default_value' => ($actividad) ? $actividad->hora : '',
        ];

        $form['fechas']['final_fecha'] = [
            '#type' => 'date',
            '#title' => t('Fecha Final'),
            '#default_value' => ($actividad) ? $actividad->final_fecha : '',
        ];

        $form['fechas']['frecuencia_dias'] = [
            '#type' => 'select',
            '#title' => t('Dia específico en que se repetirá'),
            '#options' => [
                '' => 'Seleccionar',
                1 => 'Domingo',
                2 => 'Lunes',
                3 => 'Martes',
                4 => 'Miércoles',
                5 => 'Jueves',
                6 => 'Viernes',
                7 => 'Sábado',
            ],
            '#default_value' => ($actividad) ? $actividad->frecuencia_dias : '',
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
            '#url' => Url::fromRoute('mancal_cagf.listarActividades'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        $hora = $form_state->getValue('hora');
        if (!empty($hora)) {
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $hora)) {
                $form_state->setErrorByName('hora', $this->t('La hora brindada no está en el formato solicitado'));
            }
        }

        $fecha_final = $form_state->getValue('final_fecha');
        if (date("d/m/Y", strtotime($fecha_final)) != date("d/m/Y", strtotime('31/12/1969'))) {
            $fecha_inicial = $form_state->getValue('inicio_fecha');

            //Conversion a tipo fecha
            $f_inicio = new \DateTime(date("Y-m-d", strtotime($fecha_inicial)));
            $f_final = new \DateTime(date("Y-m-d", strtotime($fecha_final)));

            //Comparacion
            if ($f_final < $f_inicio) {
                $form_state->setErrorByName('final_fecha', $this->t('La fecha final debe ser después de la inicial'));
            }
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $id = $form_state->getValue('id_actividad');
        $fecha_final = $form_state->getValue('final_fecha');
        $frecuencia_dias = $form_state->getValue('frecuencia_dias');
        $hora = $form_state->getValue('hora');
        $cancelado = $form_state->getValue('cancelado');

        $fields = [
            'titulo' => SafeMarkup::checkPlain($form_state->getValue('titulo')),
            'descripcion' => $form_state->getValue('descripcion'),
            'tipo_actividad' => $form_state->getValue('tipo_actividad'),
            'encargado' => $form_state->getValue('encargado'),
            'inicio_fecha' => $form_state->getValue('inicio_fecha'),
        ];

        if (date("d/m/Y", strtotime($fecha_final)) != date("d/m/Y", strtotime('31/12/1969'))) {
            $fields['final_fecha'] = $fecha_final;
        } else {
            $fields['final_fecha'] = NULL;
        }

        if (!empty($hora)) {
            $fields['hora'] = $hora;
        } else {
            $fields['hora'] = NULL;
        }

        if (!empty($frecuencia_dias)) {
            $fields['frecuencia_dias'] = $frecuencia_dias;
        } else {
            $fields['frecuencia_dias'] = NULL;
        }

        if (!empty($id) && ActividadesRepo::existe($id)) {
            $fields['cancelado'] = $cancelado;

            ActividadesRepo::actualizar($id, $fields);
            $message = 'Actividad ' . $fields['titulo'] . ' actualizada';
        } else {
            ActividadesRepo::agregar($fields);
            $message = 'Nueva actividad ' . $fields['titulo'] . ' guardada';
        }

        drupal_set_message($message);
        $form_state->setRedirect('mancal_cagf.listarActividades');
    }
}
