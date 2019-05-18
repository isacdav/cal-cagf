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
        $start = \Drupal::request()->query->get('start');
        $start = substr($start, 0, 10);
        $end = \Drupal::request()->query->get('end');
        $end = substr($end, 0, 10);

        $actividades_bd = ActividadesRepo::traerPorMes($start, $end);

        $response = [];

        if ($actividades_bd) {

            foreach ($actividades_bd as $act_bd) {
                $fecha_i = null;
                $fecha_f = null;
                $hora = null;

                $fecha_i = date("Y-m-d", strtotime($act_bd->inicio_fecha));

                if ($act_bd->final_fecha) {
                    $fecha_f = date("Y-m-d", strtotime($act_bd->final_fecha));
                }
                if ($act_bd->hora) {
                    $hora = $act_bd->hora . '-06:00';
                }

                $categoria = CategoriasRepo::buscarCategoria($act_bd->categoria);

                $act_res = [];
                $act_res['id'] = $act_bd->id_actividad;
                $act_res['title'] = $act_bd->titulo;
                $act_res['desc'] = $act_bd->descripcion;
                $act_res['inCharge'] = $act_bd->encargado;
                $act_res['contact'] = $act_bd->contacto;
                $act_res['link_fb'] = $act_bd->link_publicacion_fb;
                $act_res['canceled'] = $act_bd->cancelado;
                $act_res['reason'] = $act_bd->motivo_cancelacion;

                if ($act_bd->frecuencia_dias) {
                    $act_res['startRecur'] = $fecha_i;
                    $act_res['endRecur'] = $fecha_f;
                    $act_res['startTime'] = $hora;
                    $act_res['daysOfWeek'] = [ $act_bd->frecuencia_dias - 1 ];
                } else {
                    if ($act_bd->hora) {
                        $fecha_i = $fecha_i . 'T' . $act_bd->hora . '-06:00';
                    }
                    $act_res['start'] = $fecha_i;
                    $act_res['end'] = $fecha_f;
                }

                $act_res['category'] = $categoria['nombre'];
                $act_res['color'] = $categoria['color'];

                $response[] = $act_res;
            }
        } else {
            $response['mensaje'] = 'no hay actividades registradas';
        }

        return new JsonResponse($response);
    }
}
