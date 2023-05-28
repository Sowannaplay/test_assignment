<?php

namespace Drupal\api_test_assignment\Services;

use Drupal\Core\Database\Connection;

/**
 * Implements connection to api_exchange_rate_comparison table.
 */
class DatabaseConnectionHelper {

  /**
   * Database connection.
   *
   * @var Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs an DatabaseConnectionHelper object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   Database Connection.
   */
  public function __construct(Connection $database) {
    $this->connection = $database;
  }

  /**
   * Retrieves rates from db associated by Country code.
   */
  public function getRateFromDb() {
    return $this->connection
      ->select('api_exchange_rate_comparison', 'ac')
      ->fields('ac', ['country_id', 'rate'])
      ->execute()
      ->fetchAllAssoc('country_id');
  }

  /**
   * Inserts rates to database.
   */
  public function insertDb($minimal_rates) {
    $query = $this->connection->insert('api_exchange_rate_comparison');
    foreach ($minimal_rates as $country => $rate) {
      $query->fields(
        [
          'country_id',
          'rate',
        ]
      )
        ->values([$country, $rate])
        ->execute();
    }
  }

  /**
   * Updates rate by country id.
   */
  public function updateDb($country, $rate) {
    $this->connection->update('api_exchange_rate_comparison')
      ->fields(
        [
          'rate' => $rate,
        ]
      )
      ->condition('country_id', $country, '=')
      ->execute();
  }

}
