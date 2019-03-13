<?php

namespace Drupal\jVectorMap\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Regions jVectorMap' Block.
 *
 * @Block(
 *   id = "jVectorMap_region",
 *   admin_label = @Translation("jVectorMap Regions Block"),
 *   category = @Translation("jVectorMap"),
 * )
 */
class JVectorMapBlock extends BlockBase implements BlockPluginInterface {

  public function build()
  {
    return array( "#markup" => $this->t('Regions jVectorMap block') );
  }
}