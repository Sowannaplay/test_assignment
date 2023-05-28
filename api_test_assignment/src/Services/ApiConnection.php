<?php

namespace Drupal\api_test_assignment\Services;

use Drupal\api_test_assignment\ApiConnectionInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

/**
 * Implements api connection class.
 */
class ApiConnection implements ApiConnectionInterface {

  /**
   * Https client.
   *
   * @var GuzzleHttp\ClientInterface
   */
  protected $client;


  /**
   * Logger factory.
   *
   * @var Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * Error message.
   *
   * @var mixed
   */
  public $error;

  /**
   * Constructs an ApiConnection object.
   *
   * @param GuzzleHttp\ClientInterface $client
   *   Guzzle client.
   * @param Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   Logger Factory.
   */
  public function __construct(ClientInterface $client, LoggerChannelFactoryInterface $logger) {
    $this->client = $client;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getRates($url) {
    try {
      $request = new Request('GET', $url);
      $this->log($request);
      $response = $this->client->send($request);
    }
    catch (RequestException | GuzzleException $e) {
      $this->error = $e->getMessage();
      $this->logger->get('api_test_assignment_response')->notice($this->error);
      return FALSE;
    }
    $this->log($response);
    return Json::decode($response->getBody());
  }

  /**
   * Logs response/request.
   */
  protected function log($sentence) {
    $sent_headers = '';
    foreach ($sentence->getHeaders() as $name => $values) {
      $sent_headers .= $name . ': ' . implode(', ', $values) . "\r\n";
    }
    $this->logger->get('api_test_assignment_request')->info($sent_headers . $sentence->getBody());
  }

}

