<?php

namespace Drupal\mancal_cagf\Repository;

class ActividadesRepo {

    public static function listarTodos($limit = NULL, $orderBy = NULL, $order = 'DESC') {
        $query = \Drupal::database()->select('actividades', 'a') ->fields('a');

        if ($orderBy) {
            $query->orderBy($orderBy, $order);
        }

        $result = $query->execute()->fetchAll();

        return $result;
      }

      public static function existe($id) {
        $result = \Drupal::database()->select('actividades', 'a')
          ->fields('a', ['id_actividad'])
          ->condition('id_actividad', $id, '=')
          ->execute()
          ->fetchField();

        return (bool) $result;
      }

      public static function buscarUna($id) {

        $result = \Drupal::database()->select('actividades', 'a')
          ->fields('a')
          ->condition('id_actividad', $id, '=')
          ->execute()
          ->fetchObject();

        return $result;
      }

      public static function agregar(array $fields) {
        return \Drupal::database()->insert('actividades')->fields($fields)->execute();
      }

      public static function actualizar($id, array $fields) {
        return \Drupal::database()->update('actividades')->fields($fields)
          ->condition('id_actividad', $id)
          ->execute();
      }

      public static function elminar($id) {
        return \Drupal::database()->delete('actividades')->condition('id_actividad', $id)->execute();
      }

}