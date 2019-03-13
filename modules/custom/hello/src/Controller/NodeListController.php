<?php

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class NodeListController extends ControllerBase {

  /**
   * @param string $nodetype
   * @return array
   */
  public function content($nodetype = NULL) {
    $node_types = $this->entityTypeManager()->getStorage('node_type')->loadMultiple();
    $item_list = [];
    foreach ($node_types as $node_type) {
      $url = new Url('hello.hello.node_list', ['nodetype' => $node_type->id()]);
      $item_list[] = new Link($node_type->label(), $url);
    }
    $node_type_list = [
      '#theme' => 'item_list',
      '#items' => $item_list,
      '#title' => $this->t('Filter by node types'),
    ];

    $query = $this->entityTypeManager()->getStorage('node')->getQuery();
    // Si on a un argument dans l'URL, on ne cible que les noeuds correspondants.
    if ($nodetype) {
      $query->condition('type', $nodetype);
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
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Node list'),
    ];

    // Render array pour la pagination.
    $pager = ['#type' => 'pager'];

    return [
      'node_type_list' => $node_type_list,
      'list' => $list,
      'pager' => $pager,
      '#cache' => [
        'keys' => ['hello:node_list'],
        'tags' => ['node_list', 'node_type_list'],
      ],
    ];
  }

}
