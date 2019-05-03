<?php

namespace Drupal\mancal_cagf\Repository;

class CategoriasRepo
{

    private static $lista_categorias = [
        ['id_cat' => 0, 'nombre' => 'Taller', 'Color' => '#f79c1d'],
        ['id_cat' => 1, 'nombre' => 'Exposición', 'Color' => '#7fa99b'],
        ['id_cat' => 2, 'nombre' => 'Baile/Danza', 'Color' => '#f6e79c'],
        ['id_cat' => 3, 'nombre' => 'Música', 'Color' => '#1c4b82'],
        ['id_cat' => 4, 'nombre' => 'Literatura', 'Color' => '#85ef47'],
        ['id_cat' => 5, 'nombre' => 'Teatro', 'Color' => '#913535'],
        ['id_cat' => 6, 'nombre' => 'Cultura Pop', 'Color' => '#cdffeb'],
        ['id_cat' => 7, 'nombre' => 'Homenaje', 'Color' => '#c5c5c5'],
        ['id_cat' => 8, 'nombre' => 'Tertulia', 'Color' => '#c7b198'],
        ['id_cat' => 9, 'nombre' => 'Otros', 'Color' => '#c70039'],
    ];

    public static function getCategorias()
    {
        return self::$lista_categorias;
    }

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
