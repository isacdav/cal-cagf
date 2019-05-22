<?php

namespace Drupal\mancal_cagf\Repository;

class CategoriasRepo
{
    //Clase para guardar y retornar las categorias
    //Se creo asi por solicitud
    private static $lista_categorias = [
        ['id_cat' => 0, 'nombre' => 'Taller', 'color' => '#ff8264'],
        ['id_cat' => 1, 'nombre' => 'Exposición', 'color' => '#7fa99b'],
        ['id_cat' => 2, 'nombre' => 'Baile', 'color' => '#b97ab0'],
        ['id_cat' => 3, 'nombre' => 'Música', 'color' => '#4592af'],
        ['id_cat' => 4, 'nombre' => 'Literatura', 'color' => '#f6e79c'],
        ['id_cat' => 5, 'nombre' => 'Teatro', 'color' => '#c5f8c8'],
        ['id_cat' => 6, 'nombre' => 'Cultura Pop', 'color' => '#c19191'],
        ['id_cat' => 7, 'nombre' => 'Homenaje', 'color' => '#c5c5c5'],
        ['id_cat' => 8, 'nombre' => 'Tertulia', 'color' => '#bfcd7e'],
        ['id_cat' => 9, 'nombre' => 'Otros', 'color' => '#c7b198'],
    ];

    //Devuelve la lista
    public static function getCategorias()
    {
        return self::$lista_categorias;
    }

    //Busca una categoria en especifico
    public static function buscarCategoria($id)
    {
        foreach (self::$lista_categorias as $cat) {
            if ($cat['id_cat'] == $id) {
                return $cat;
            }
        }
        return null;
    }
}
