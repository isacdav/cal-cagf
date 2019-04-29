<?php

namespace Drupal\mancal_cagf\Repository;

class TiposRepo
{

    public static function listarTodos($limit = NULL, $orderBy = NULL, $order = 'DESC')
    {
        $query = \Drupal::database()->select('tipos_actividades', 't')->fields('t');

        if ($orderBy) {
            $query->orderBy($orderBy, $order);
        }

        $result = $query->execute()->fetchAll();

        return $result;
    }

    public static function existe($id)
    {
        $result = \Drupal::database()->select('tipos_actividades', 't')
            ->fields('t', ['id_tipo'])
            ->condition('id_tipo', $id, '=')
            ->execute()
            ->fetchField();

        return (bool)$result;
    }

    public static function buscarUna($id)
    {

        $result = \Drupal::database()->select('tipos_actividades', 't')
            ->fields('t')
            ->condition('id_tipo', $id, '=')
            ->execute()
            ->fetchObject();

        return $result;
    }

    public static function agregar(array $fields)
    {
        return \Drupal::database()->insert('tipos_actividades')->fields($fields)->execute();
    }

    public static function actualizar($id, array $fields)
    {
        return \Drupal::database()->update('tipos_actividades')->fields($fields)
            ->condition('id_tipo', $id)
            ->execute();
    }

    public static function elminar($id)
    {
        return \Drupal::database()->delete('tipos_actividades')->condition('id_tipo', $id)->execute();
    }
}
