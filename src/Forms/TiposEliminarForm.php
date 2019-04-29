<?php

namespace Drupal\mancal_cagf\Forms;

use Drupal;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mancal_cagf\Repository\TiposRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TiposEliminarForm extends ConfirmFormBase
{
    protected $id;

    public function getFormId()
    {
        return 'tipos_eliminar';
    }

    public function getQuestion()
    {
        return t('¿Desea eliminar el tipo de actividad %id?', ['%id' => $this->id]);
    }

    public function getConfirmText()
    {
        return t('Eliminar');
    }

    public function getCancelRoute()
    {
        return new Url('mancal_cagf.listarTipos');
    }

    public function getCancelUrl()
    {
        return new Url('mancal_cagf.listarTipos');
    }

    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL)
    {
        if (!TiposRepo::existe($id)) {
            drupal_set_message(t('Tipo de actividad no válido'), 'error');
            return new RedirectResponse(Drupal::url('mancal_cagf.listarTipos'));
        }
        $this->id = $id;
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        TiposRepo::elminar($this->id);
        drupal_set_message(t('El tipo de actividad %id ha sido elminado', ['%id' => $this->id]));
        $form_state->setRedirect('mancal_cagf.listarTipos');
    }
}
