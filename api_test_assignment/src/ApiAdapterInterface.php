<?php

namespace Drupal\api_test_assignment;

/**
 * Interface definition for api interface.
 */
interface ApiAdapterInterface {

  /**
   * Get Rates from ApiConnecter and fetches them .
   *
   * @param string $url
   *   Api Url.
   *
   * @return mixed
   *   Rates or false in case of error.
   */
  public function getRates(string $url);

}
