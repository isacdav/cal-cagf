<?php

namespace Drupal\mancal_cagf\Repository;

class CategoriasRepo
{

    private static $lista_categorias = [
        ['id_cat' => 0, 'nombre' => 'Taller', 'color' => '#f79c1d'],
        ['id_cat' => 1, 'nombre' => 'Exposición', 'color' => '#7fa99b'],
        ['id_cat' => 2, 'nombre' => 'Baile/Danza', 'color' => '#f6e79c'],
        ['id_cat' => 3, 'nombre' => 'Música', 'color' => '#1c4b82'],
        ['id_cat' => 4, 'nombre' => 'Literatura', 'color' => '#85ef47'],
        ['id_cat' => 5, 'nombre' => 'Teatro', 'color' => '#913535'],
        ['id_cat' => 6, 'nombre' => 'Cultura Pop', 'color' => '#cdffeb'],
        ['id_cat' => 7, 'nombre' => 'Homenaje', 'color' => '#c5c5c5'],
        ['id_cat' => 8, 'nombre' => 'Tertulia', 'color' => '#c7b198'],
        ['id_cat' => 9, 'nombre' => 'Otros', 'color' => '#c70039'],
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
