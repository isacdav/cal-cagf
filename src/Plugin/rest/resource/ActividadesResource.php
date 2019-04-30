<?php

namespace Drupal\mancal_cagf\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\mancal_cagf\Repository\ActividadesRepo;

/**
 * Provides an API for the calendar
 *
 * @RestResource(
 *   id = "mancal_cagf",
 *   label = @Translation("Actividades REST api"),
 *   uri_paths = {
 *     "canonical" = "/api/mancal_cagf"
 *   }
 * )
 */
class ActividadesResource extends ResourceBase
{

    /**
     * Responds to entity GET requests.
     * @return \Drupal\rest\ResourceResponse
     */
    public function get()
    {
        $actividades_bd = ActividadesRepo::listarTodos();

        $response = [];
        foreach($actividades_bd as $act) {
            $response = [
                'id' => $act->id_actividad,
                'title' => $act->titulo,
                'desc' => $act->descripcion,
                'start' => $act->inicio_fecha,
                'end' => $act->final_fecha,
                'daysOfWeek' => $act->frecuencia_dias,
            ];
        }

        return new ResourceResponse($response);
    }

}
