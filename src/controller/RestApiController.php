<?php

namespace Drupal\mancal_cagf\controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mancal_cagf\Repository\ActividadesRepo;
use Drupal\mancal_cagf\Repository\CategoriasRepo;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestApiController extends ControllerBase
{

    public function getActividades()
    {
        $actividades_bd = ActividadesRepo::listarTodos();

        $response = [];
        
        if ($actividades_bd) {
            foreach ($actividades_bd as $act) {
                $fecha_i = null;
                $fecha_f = null;
                $hora = null;
                
                $fecha_i = date("Y-m-d", strtotime($act->inicio_fecha));
                
                if ($act->final_fecha) {
                    $fecha_f = date("Y-m-d", strtotime($act->final_fecha));
                }
                if ($act->hora) {
                    $hora = $act->hora . '-06:00';
                }
                
                $categoria = CategoriasRepo::buscarCategoria($act->categoria);
                
                if ($act->frecuencia_dias) {
                    $response[] = array(
                        'id' => $act->id_actividad,
                        'title' => $act->titulo,
                        'desc' => $act->descripcion,
                        'inCharge' => $act->encargado,
                        'contact' => $act->contacto,
                        'link_fb' => $act->link_publicacion_fb,
                        'canceled' => $act->cancelado,
                        'reason' => $act->motivo_cancelacion,
                        'startRecur' => $fecha_i,
                        'endRecur' => $fecha_f,
                        'startTime' => $hora,
                        'daysOfWeek' => $act->frecuencia_dias - 1,
                        'category' => $categoria['nombre'],
                        'color' => $categoria['color'],
                    );
                } else {
                    if ($act->hora) {
                        $fecha_i = $fecha_i . 'T' . $act->hora . '-06:00';
                    }
                    $response[] = array(
                        'id' => $act->id_actividad,
                        'title' => $act->titulo,
                        'desc' => $act->descripcion,
                        'inCharge' => $act->encargado,
                        'contact' => $act->contacto,
                        'link_fb' => $act->link_publicacion_fb,
                        'canceled' => $act->cancelado,
                        'reason' => $act->motivo_cancelacion,
                        'start' => $fecha_i,
                        'end' => $fecha_f,
                        'category' => $categoria['nombre'],
                        'color' => $categoria['color'],
                    );
                }
                
            }
        } else {
            $response['mensaje'] = 'no hay actividades registradas';
        }

        return new JsonResponse($response);
    }
}
