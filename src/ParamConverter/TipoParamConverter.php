<?php

namespace Drupal\mancal_cagf\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;
use Drupal\mancal_cagf\Repository\TiposRepo;

class TipoParamConverter implements ParamConverterInterface {

    public function convert($value, $definition, $name, array $defaults) {
        if (!TiposRepo::existe($value)) {
            return 'invalid';
        }
        return TiposRepo::buscarUna($value);
    }

    public function applies($definition, $name, Route $route) {
        return (!empty($definition['type']) && $definition['type'] == 'tipo');
    }

}
