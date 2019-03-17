<?php

namespace Drupal\research_core\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class Research_coreController extends ControllerBase {

  public function content()
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
}
