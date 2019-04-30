<?php

namespace Drupal\mancal_cagf\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

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
        $nombre = 'November Rain';
        $desc = NULL;

        $response = [
            'id' => '11',
            'nombre' => $nombre,
            'desc' => $desc
            ];
        return new ResourceResponse($response);
    }
}
