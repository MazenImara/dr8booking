<?php

namespace Drupal\dr8booking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use \Drupal\dr8booking\Functions\fun;

/**
 * Provides a 'booking' Block.
 *
 * @Block(
 *   id = "item_booked_users",
 *   admin_label = @Translation("Item booked users block"),
 *   category = @Translation("Booking"),
 * )
 */
class ItemBookedUsers extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      // You can get nid and anything else you need from the node object.
      $nid = $node->id();
    }
    $node = fun::getNode($nid);
    $currentUser = fun::getCurrentUser();
    $showUsers = False;
    if (($node['obj']->getOwner()->id() == $currentUser['id']) or (in_array('administrator', $currentUser['roles']))) {
      $showUsers = True;
    }
    return [
      '#theme' =>  'booked_users',
      '#cache' => ['max-age' => 0],
      '#content' => [
        'users' => fun::getItemBookedUsers($nid),
        'showUsers' => $showUsers,
      ],
    ];

  }

}
