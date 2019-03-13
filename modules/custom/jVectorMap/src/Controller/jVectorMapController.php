<?php

namespace Drupal\jVectorMap\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class JVectorMapController extends ControllerBase {

  public function regionsMap()
  {
    return [
        "#theme"  => 'jVectorMap_tpl',
        "#area"   => 'regions',
        "#load"   => 'fr_regions_mill',
        '#attached' => [
          'library' => [
            'jVectorMap/jVectorMap',
          ],
        ],
    ];
  }

  public function departementsMap()
  {
    return [
        "#theme"  => 'jVectorMap_tpl',
        "#area"   => 'departements',
        "#load"   => 'fr_mill',
        '#attached' => [
          'library' => [
            'jVectorMap/jVectorMap',
          ],
        ],
    ];
  }

}
