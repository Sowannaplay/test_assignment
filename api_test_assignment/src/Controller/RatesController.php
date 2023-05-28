<?php

namespace Drupal\api_test_assignment\Controller;

use Drupal\api_test_assignment\ApiAdapterInterface;
use Drupal\api_test_assignment\Services\DatabaseConnectionHelper;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Rates controller class.
 */
class RatesController extends ControllerBase {
  /**
   * Api adapter.
   *
   * @var Drupal\api_test_assignment\ApiAdapterInterface
   */
  public $adapter;

  /**
   * Table connection.
   *
   * @var Drupal\api_test_assignment\DatabaseConnectionHelper
   */
  public $connection;

  /**
   * Constructs an RatesController object.
   *
   * @param Drupal\api_test_assignment\ApiAdapterInterface $adapter
   *   Api connection.
   * @param Drupal\api_test_assignment\DatabaseConnectionHelper $connection
   *   Table connection.
   */
  public function __construct(ApiAdapterInterface $adapter, DatabaseConnectionHelper $connection) {
    $this->adapter = $adapter;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('api_test_assignment.api_adapter'),
      $container->get('api_test_assignment.db_helper')
    );
  }

  /**
   * Gets rates and writes cheapest to db.
   */
  public function writeRates() {
    $rates = [
      $this->adapter->getRates('https://www.mocky.io/v3/81c1dc29-bac5-4ee0-a577-4f47432a3206'),
      $this->adapter->getRates('https://www.mocky.io/v3/01dac20a-0b85-462a-a0c4-6ad38cba7109'),
    ];
    $minimal_rates = $this->compareRates($rates);
    $current_rates = $this->connection->getRateFromDb();
    $rates_to_insert = array_diff_key($minimal_rates, $current_rates);
    $rates_to_update = array_intersect_key($minimal_rates, $current_rates);
    $this->connection->insertDb($rates_to_insert);
    foreach ($rates_to_update as $country => $rate) {
      $this->connection->updateDb($country, $rate);
    }
    if ($this->adapter->getError()) {
      return ['#markup' => 'One or more API calls failed, please check the logs for more information'];
    }
    return ['#markup' => 'Database updated with new values. Thanks for the update'];
  }

  /**
   * Renders cheapest rates.
   */
  public function showRates() {
    if (!$rates = $this->connection->getRateFromDb()) {
      return ['#markup' => 'No rates stored in db yet'];
    }
    return [
      '#theme' => 'rate_template',
      '#rates' => $rates,
    ];
  }

  /**
   * Compares rates.
   */
  protected function compareRates($rates) {
    $minimal_rates = [];
    foreach ($rates as $currency) {
      if (!$currency) {
        continue;
      }
      foreach ($currency as $country => $api_rate) {
        foreach ($api_rate as $country_rate) {
          if (!isset($minimal_rates[$country]) || $country_rate < $minimal_rates[$country]) {
            $minimal_rates[$country] = (float) $country_rate;
          }
        }
      }
    }
    return $minimal_rates;
  }

}
