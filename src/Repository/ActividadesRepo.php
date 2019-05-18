<?php

namespace Drupal\mancal_cagf\Repository;

class ActividadesRepo
{
    public static function listarTodos($limit = NULL, $orderBy = NULL, $order = 'DESC')
    {
        $query = \Drupal::database()->select('actividades', 'a')->fields('a');

        if ($orderBy) {
            $query->orderBy($orderBy, $order);
        }

        $result = $query->execute()->fetchAll();

        return $result;
    }

    public static function traerPorMes($start, $end)
    {
        $fecha_inicio_cal = date("Y-m-d", strtotime($start));
        $fecha_final_cal = date("Y-m-d", strtotime($end));

        $result = \Drupal::database()->select('actividades', 'a')
            ->fields('a')
            ->where('(inicio_fecha >= :inicio_param AND inicio_fecha <= :final_param) OR' . //Si fecha de inicio esta dentro del rango
                    '(final_fecha >= :inicio_param AND final_fecha <= :final_param) OR' . //Si fecha final esta dentro del rango
                    '(inicio_fecha <= :inicio_param AND final_fecha >= :final_param)', //Si el rango esta dentro de fechas de inicio y final
                array(
                    ':inicio_param' => $fecha_inicio_cal,
                    ':final_param' => $fecha_final_cal,
                )
            )
            ->execute()
            ->fetchAll();
        return $result;
    }


    public static function existe($id)
    {
        $result = \Drupal::database()->select('actividades', 'a')
            ->fields('a', ['id_actividad'])
            ->condition('id_actividad', $id, '=')
            ->execute()
            ->fetchField();

        return (bool)$result;
    }

    public static function buscarUna($id)
    {

        $result = \Drupal::database()->select('actividades', 'a')
            ->fields('a')
            ->condition('id_actividad', $id, '=')
            ->execute()
            ->fetchObject();

        return $result;
    }

    public static function agregar(array $fields)
    {
        return \Drupal::database()->insert('actividades')->fields($fields)->execute();
    }

    public static function actualizar($id, array $fields)
    {
        return \Drupal::database()->update('actividades')->fields($fields)
            ->condition('id_actividad', $id)
            ->execute();
    }

    public static function elminar($id)
    {
        return \Drupal::database()->delete('actividades')->condition('id_actividad', $id)->execute();
    }
}
