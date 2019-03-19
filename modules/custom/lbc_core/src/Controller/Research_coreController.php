<?php

namespace Drupal\research_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;

/**
 *
 */
class Research_coreController extends ControllerBase {

  public function content()
  {
    // $node_types = $this->entityTypeManager()->getStorage('node_type')->loadMultiple();
    // var_dump($node_types);
    // $item_list = [];
    // foreach ($node_types as $node_type) {
    //   $url = new Url('hello.hello.node_list', ['type' => $node_type->id()]);
    //   $item_list[] = new Link($node_type->label(), $url);
    // }
    // $node_type_list = [
    //   '#theme' => 'item_list',
    //   '#items' => $item_list,
    //   '#title' => $this->t('Filter by node types'),
    // ];



    $query = $this->entityTypeManager()->getStorage('node')->getQuery();
    // Si on a un argument dans l'URL, on ne cible que les noeuds correspondants.
    $query->condition('type', 'annonce');

    // Check if multiple object type researched
    $params = \Drupal::request()->query->get('type');
    if( $annonce_types  = \Drupal::request()->query->get('annonce_type') ){
      $annonce_types = explode( ",", $annonce_types);
      $query->condition('field_annonce_type', $annonce_types, "IN" );
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

    // On construit une requête paginée.
    $nids = $query->pager(10)->execute();
    // Charge les noeuds correspondants au résultat de la requête.
    $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
    // Construit un tableau de liens vers les noeuds.
    $items = [];
    foreach ($nodes as $node) {
      $items[] = $node->toLink();
    }

    $list = [
      '#theme' => 'annonces_list',
      '#annonces' => $nodes,
    ];

    // Render array pour la pagination.
    $pager = ['#type' => 'pager'];

    return [
      'pager' => $pager,
      'list'  => $list,
      'pager' => $pager,
    ];
  }
}
