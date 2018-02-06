<?php

namespace Drupal\dr8booking\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\dr8booking\Functions\fun;
#use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\Core\Url;

class bookingController extends ControllerBase {
  /**
   * Display the markup.
   *
   * @return array
   */
  public function book($nid, $samePage) {
    return fun::book($nid, $samePage);
  }
  /**
   * Display the markup.
   *
   * @return array
   */
  public function cancel($nid) {
    return fun::cancel($nid);
  }
  /**
   * Display the markup.
   *
   * @return array
   */
  public function myBooking() {
    $uid = fun::getCurrentUser()['id'];
    return new RedirectResponse(fun::getMybookingUrl($uid));
  }

  public function test() {
        $url = '/';
    $view = \Drupal\views\Views::getView('booking1');
    if ($view) {
      $url = Url::fromRoute('view.booking.page_2', ['arg_0' => 1])->toString();
    }

    return [
      '#theme' =>  'booked_users',
      '#content' => [
        'test' => $url
      ],
    ];
  }
}
