<?php

namespace Drupal\lbc_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;

/**
 *
 */
class ResearchController extends ControllerBase {

  public function content()
  {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery();
    // Si on a un argument dans l'URL, on ne cible que les noeuds correspondants.
    $query->condition('type', 'annonce');

    // Check if multiple object type researched
    $params = \Drupal::request()->query->get('type');
    if( $annonce_types  = \Drupal::request()->query->get('annonce_type') ){
      $annonce_types = explode( ",", $annonce_types);
      $query->condition('field_annonce_type', $annonce_types, "IN" );
    }
    
    if( $cp  = \Drupal::request()->query->get('cp') ){
      $query->condition('field_code_postal', $cp , "IN" );
    }

    if( $villes  = \Drupal::request()->query->get('villes') ){
      $villes = explode( ",", $villes);
      $query->condition('field_ville', $villes, "IN" );
    }

    if( $dpts  = \Drupal::request()->query->get('dpts') ){
      $dpts = explode( ",", $dpts);
      $query->condition('field_departement', $dpts, "IN" );
    }

    if( $regions  = \Drupal::request()->query->get('regions') ){
      $regions = explode( ",", $regions);
      $query->condition('field_region', $regions, "IN" );
    }

    if( $prixMin  = \Drupal::request()->query->get('prixMin') ){
      $query->condition('field_prix', $prixMin, ">" );
    }

    if( $prixMax  = \Drupal::request()->query->get('prixMax') ){
      $query->condition('field_prix', $prixMax , "<" );
    }

    if( $term  = \Drupal::request()->query->get('term') ){
      $query->condition('title', "%".$term."%" , "LIKE" );
      if( ! $onlytitle  = \Drupal::request()->query->get('onlytitle') ){
        $query->condition('body', "%".$term."%" , "LIKE" );
      }
    }

    if( $tags  = \Drupal::request()->query->get('type') ){
      $query->condition('field_annonce_tags', $tags , "IN" );
    }

    // On construit une requête paginée.
    $nids = $query->pager(10)->execute();
    // Charge les noeuds correspondants au résultat de la requête.
    $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
    // Construit un tableau de liens vers les noeuds.
    $items = [];
    foreach ($nodes as $node) {
      $items[] = $node->toLink();
    }

    //Get reserch form
    //$form = \Drupal::formBuilder()->getForm('Drupal\lbc_core\Form\ResearchForm');

    $list = [
      '#theme' => 'annonces_list',
      '#annonces' => $nodes,
    ];

    // Render array pour la pagination.
    $pager = ['#type' => 'pager'];
    $pager2 = ['#type' => 'pager'];

    return [
      'pager' => $pager,
      'list'  => $list,
      'pager' => $pager2,
    ];
  }
}
