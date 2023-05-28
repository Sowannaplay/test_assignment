<?php

namespace Drupal\api_test_assignment;

/**
 * Interface definition for api connection.
 */
interface ApiConnectionInterface {

  /**
   * Connects to Api to get Rates.
   *
   * @param string $url
   *   Api Url.
   *
   * @return mixed
   *   Rates or false in case of error.
   */
  public function getRates(string $url);

}
