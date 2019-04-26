<?php

namespace Drupal\mancal_cagf\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;
use Drupal\mancal_cagf\Repository\ActividadesRepo;

class ActividadParamConverter implements ParamConverterInterface {

    public function convert($value, $definition, $name, array $defaults) {
        if (!ActividadesRepo::existe($value)) {
            return 'invalid';
        }
        return ActividadesRepo::buscarUna($value);
    }

    public function applies($definition, $name, Route $route) {
        return (!empty($definition['type']) && $definition['type'] == 'actividad');
    }

}
