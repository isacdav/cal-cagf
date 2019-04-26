<?php

namespace Drupal\mancal_cagf\Forms;

use Drupal;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mancal_cagf\Repository\ActividadesRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ActividadesEliminarForm extends ConfirmFormBase
{

    protected $id;

    public function getFormId()
    {
        return 'actividades_elminar';
    }

    public function getQuestion()
    {
        return t('¿Desea elminar la actividad %id?', ['%id' => $this->id]);
    }

    public function getConfirmText()
    {
        return t('Eliminar');
    }

    public function getCancelRoute()
    {
        return new Url('mancal_cagf.listarActividades');
    }

    public function getCancelUrl()
    {
        return new Url('mancal_cagf.listarActividades');
    }

    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
    {
        if (!ActividadesRepo::existe($id)) {
            drupal_set_message(t('Actividad no válida'), 'error');
            return new RedirectResponse(Drupal::url('mancal_cagf.listarActividades'));
        }
        $this->id = $id;
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        ActividadesRepo::elminar($this->id);
        drupal_set_message(t('La actividad %id ha sido elminada', ['%id' => $this->id]));
        $form_state->setRedirect('mancal_cagf.listarActividades');
    }
}
