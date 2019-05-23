<?php

namespace Drupal\mancal_cagf\controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mancal_cagf\Repository\ActividadesRepo;
use Drupal\mancal_cagf\Repository\CategoriasRepo;
use Symfony\Component\HttpFoundation\JsonResponse;

//API que devuelve las actividades por metodo GET
class ApiController extends ControllerBase
{

    public function getActividades()
    {
        //Solicita los parametros start - end
        //Deben venir como parametros
        $start = \Drupal::request()->query->get('start');
        $start = substr($start, 0, 10);
        $end = \Drupal::request()->query->get('end');
        $end = substr($end, 0, 10);
        //El subtr es para que tome solamente la fecha

        //Pide a la bd las actividades
        $actividades_bd = ActividadesRepo::traerPorMes($start, $end);

        //Se crea el array que se retornara
        $response = [];

        if ($actividades_bd) {

            //Se recorre el resultado
            foreach ($actividades_bd as $act_bd) {
                $fecha_i = null;
                $fecha_f = null;
                $hora = null;

                //Se convierten las fechas y hora
                $fecha_i = date("Y-m-d", strtotime($act_bd->inicio_fecha));

                if ($act_bd->final_fecha) {
                    $fecha_f = date("Y-m-d", strtotime($act_bd->final_fecha));
                }
                if ($act_bd->hora) {
                    $hora = $act_bd->hora . '-06:00';
                }

                //Se busca la categoria
                $categoria = CategoriasRepo::buscarCategoria($act_bd->categoria);

                //Se crea el array de la actividad
                //Se llena con los atributos que ocupa FullCalendar
                $act_res = [];
                $act_res['id'] = $act_bd->id_actividad;
                $act_res['title'] = $act_bd->titulo;
                $act_res['desc'] = $act_bd->descripcion;
                $act_res['inCharge'] = $act_bd->encargado;
                $act_res['contact'] = $act_bd->contacto;
                $act_res['link_fb'] = $act_bd->link_publicacion_fb;
                $act_res['canceled'] = $act_bd->cancelado;
                $act_res['reason'] = $act_bd->motivo_cancelacion;

                //Si la actividad tiene frecuencia (repeticion)
                if ($act_bd->frecuencia_dias) {
                    //Se asignan los atributos que le indican a FullCalendar que se repite
                    $act_res['startRecur'] = $fecha_i;
                    $act_res['endRecur'] = $fecha_f;
                    $act_res['startTime'] = $hora;
                    $act_res['daysOfWeek'] = [$act_bd->frecuencia_dias - 1];
                } else {
                    //Si no, asigna los necesarios
                    //Si la actividad tiene hora, se concatena a la fecha inicial
                    if ($act_bd->hora) {
                        $fecha_i = $fecha_i . 'T' . $act_bd->hora . '-06:00';
                    }
                    $act_res['start'] = $fecha_i;
                    $act_res['end'] = $fecha_f;
                }

                $act_res['category'] = $categoria['nombre'];

                //Si esta cancelado cambia los colores que llevan normalmente
                if ($act_bd->cancelado) {
                    $act_res['color'] = '#c82121';
                    $act_res['textColor'] = '#fff';
                } else {
                    $act_res['color'] = $categoria['color'];
                }

                //al array de respuesta, se le inserta el array de actividad recien creado
                $response[] = $act_res;
            }
        } else {
            $response['mensaje'] = 'no hay actividades registradas';
        }

        return new JsonResponse($response);
    }
}
