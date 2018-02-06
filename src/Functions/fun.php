<?php

namespace Drupal\dr8booking\Functions;

use  \Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\Core\Url;

class fun {

  static public function getCurrentUser()
  {
    $userId = \Drupal::currentUser()->id();
    $user = User::load($userId);
    $userArr = [
      'id' => $userId,
      'name' => $user->get('name')->value,
      'email' => $user->get('mail')->value,
      'roles' => \Drupal::currentUser()->getRoles(),
    ];
    return $userArr;
  }

  static public function getNode($nid)
  {
    $node = \Drupal\node\Entity\Node::load($nid);
    $item = [
      'id' => $nid,
      'max' => $node->field_book_item_max->value,
      'userBookedIds' => [],
      'status' => $node->field_book_item_status->value,
      'obj' => $node,
    ];
    if (!is_null($node->field_users_booked_item_ids->value)) {
      $length = count($node->field_users_booked_item_ids);
      for ($counter = 0; $counter < $length; $counter++) {
        array_push($item['userBookedIds'], $node->field_users_booked_item_ids[$counter]->value);
      }
    }
    return $item;
  }

  static public function setNodeStatus($node)
  {
    if ($node['max'] <= count($node['obj']->field_users_booked_item_ids)) {
      $node['obj']->field_book_item_status->setValue(['value' => False]);
    }
    else{
      $node['obj']->field_book_item_status->setValue(['value' => True]);
    }
    $node['obj']->save();
  }

  static public function checkUser()
  {
    $user = self::getCurrentUser();
    if (in_array('authenticated', $user['roles'])) {
      return True;
    }
    else{
      return False;
    }
  }

  static public function book($nid, $samePage)
  {
    $user = self::getCurrentUser();
    $node = self::getNode($nid);
    if (!in_array($user['id'], $node['userBookedIds']) && $node['status'] && self::checkUser()) {
      $node['obj']->field_users_booked_item_ids->appendItem($user['id']);
      $node['obj']->save();
      self::setNodeStatus($node);
      return new RedirectResponse(self::getMybookingUrl($user['id']));
    }
    else{
      if (!$node['status']) {
        drupal_set_message(t('This item is not availble'), 'error');
      }
      if (!self::checkUser()) {
        drupal_set_message(t('You have to login first'), 'error');
      }
      if (in_array($user['id'], $node['userBookedIds'])) {
        drupal_set_message(t('You have already booked this item '), 'error');
      }
      if ($samePage == 'node') {
        return new RedirectResponse('/node/' . $node['id']);
      }
      else{
        return new RedirectResponse('/' . $samePage);
      }

    }
  }
  static public function cancel($nid)
  {
    $user = self::getCurrentUser();
    $node = self::getNode($nid);
    if (in_array($user['id'], $node['userBookedIds'])) {
      if (!is_null($node['obj']->field_users_booked_item_ids->value)) {
        $length = count($node['obj']->field_users_booked_item_ids);
        for ($counter = 0; $counter < $length; $counter++) {
          if ($user['id'] == $node['obj']->field_users_booked_item_ids[$counter]->value) {
            unset($node['obj']->field_users_booked_item_ids[$counter]);
          }
        }
      }
      $node['obj']->save();
      self::setNodeStatus($node);
    }
    return new RedirectResponse(self::getMybookingUrl($user['id']));
  }

  static public function getItemBookedUsers($nid)
  {
    $node = self::getNode($nid);
    $users = [];
    foreach ($node['userBookedIds'] as $uid) {
      array_push($users, self::getUser($uid));
    }
    return $users;
  }

  static public function getUser($uid)
  {
    $user = User::load($uid);
    $userArr = [
      'id' => $uid,
      'name' => $user->get('name')->value,
      'email' => $user->get('mail')->value,
      'roles' => \Drupal::currentUser()->getRoles(),
    ];
    return $userArr;
  }
  static public function getMybookingUrl($uid)
  {
    $url = '/';
    $url = Url::fromRoute('view.booking.page_2', ['arg_0' => $uid])->toString();
    return $url;
  }
}
