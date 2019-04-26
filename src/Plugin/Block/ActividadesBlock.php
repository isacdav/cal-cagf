<?php

namespace Drupal\mancal_cagf\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\mancal_cagf\Repository\ActividadesRepo;

define("MAX_LIMIT", 7);
define("DEFAULT_LIMIT", 5);

class ActividadesBlock extends BlockBase {

    public function build() {
        $content = [];

        $content['table'] = [
            '#lazy_builder' => [static::class . '::lazyBuildTablaActividades', ],
            '#create_placeholder' => TRUE,
        ];

        $content['more'] = [
            '#type' => 'link',
            '#title' => t('More'),
            '#url' => new Url('mancal_cagf.listarActividades'),
            '#attributes' => ['class' => 'button'],
        ];

        return $content;
    }

    public static function lazyBuildTablaActividades() {
        $header = [
          'name' => t('Actividad N°'),
          'message' => t('Nombre de Actividad'),
        ];

        $rows = [];

        foreach (ActividadesRepo::listarTodos('id', 'DESC') as $id => $row) {
          $rows[] = [
            'data' => [$row->id_actividad, $row->titulo],
          ];
        }

        return [
          'table' => [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => [
              'id' => 'bd-contact-block-table',
            ],
          ],
        ];
      }

      public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);

        $config = $this->getConfiguration();

        $form['limit'] = [
          '#type' => 'textfield',
          '#title' => t('Limite'),
          '#description' => t('Numero de actividades a mostrar'),
          '#default_value' => isset($config['limit']) ?
          $config['limit'] : '',
        ];
    
        return $form;
      }

}