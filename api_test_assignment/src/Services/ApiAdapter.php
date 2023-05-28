<?php

namespace Drupal\api_test_assignment\Services;

use Drupal\api_test_assignment\ApiAdapterInterface;
use Drupal\api_test_assignment\ApiConnectionInterface;

/**
 * Implements api adapter class.
 */
class ApiAdapter implements ApiAdapterInterface {

  /**
   * Api connection.
   *
   * @var Drupal\api_test_assignment\ApiConnectionInterface
   */
  public $apiConnection;

  /**
   * Constructs an ApiAdapter object.
   *
   * @param Drupal\api_test_assignment\ApiConnectionInterface $apiConnection
   *   Api connection.
   */
  public function __construct(ApiConnectionInterface $apiConnection) {
    $this->apiConnection = $apiConnection;
  }

  /**
   * {@inheritdoc}
   */
  public function getRates($url) {
    $body = $this->apiConnection->getRates($url);
    return $this->fetchRates($body);
  }

  /**
   * Fetches rates.
   */
  public function fetchRates($body) {
    $rates = [];
    if (!is_array($body)) {
      return NULL;
    }
    if (count($body) == 1) {
      $body = $body[array_key_first($body)];
    }
    foreach ($body as $elements) {
      foreach ($elements as $key => $element) {
        if (!filter_var($element, FILTER_VALIDATE_FLOAT)) {
          if ((str_contains(strtoupper($element), 'US') || str_contains(strtoupper($element), 'DOL'))) {
            $rates['US'] = $elements;
            unset($rates['US'][$key]);
          }
          elseif ((str_contains(strtoupper($element), 'GB') || str_contains(strtoupper($element), 'PO'))) {
            $rates['GB'] = $elements;
            unset($rates['GB'][$key]);
          }
          elseif (str_contains(strtoupper($element), 'EU')) {
            $rates['EU'] = $elements;
            unset($rates['EU'][$key]);
          }
        }
      }
    }
    return $rates;
  }

  /**
   * Gives Api error message.
   */
  public function getError() {
    return $this->apiConnection->error;
  }

}
