<?php

namespace KairosDB;

use Exception;
use Guzzle\Http\Client as httpClient;

/**
 * Class Client
 *
 * @package KairosDB
 */
class Client
{
    const API_VERSION = 'v1';

    /**
     * @var string
     */
    private $base_uri;

    /**
     * @var httpClient
     */
    private $httpClient;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host = 'localhost', $port = 8080)
    {
        $this->base_uri = sprintf('http://%s:%s/api/%s/',
            $host, $port, self::API_VERSION
        );

        $this->httpClient = new httpClient($this->base_uri);
    }

    /**
     * @param $metricName
     * @param $value
     * @param array $tags
     * @param null $timestamp
     * @return string
     */
    public function addDataPoint($metricName, $value, array $tags, $timestamp = null)
    {
        return $this->post('datapoints', array(
            'name' => $metricName,
            'tags' => $tags,
            'value' => $value,
            'timestamp' => $timestamp ? $timestamp : round(microtime(true) * 1000)
        ));
    }

    /**
     * @param DataPointCollection $dataPoints
     * @return string
     */
    public function addDataPoints(DataPointCollection $dataPoints)
    {
        return $this->post('datapoints', $dataPoints->toArray());
    }

    /**
     * @param array $query
     * @return string
     */
    public function query(array $query)
    {
        return $this->post('datapoints/query', $query);
    }

    /**
     * @return string
     */
    public function queryTags()
    {
        $data = array(
            'start_absolute' => 1357023600000,
            'end_relative' => array(
                'value' => '5',
                'unit' => 'days'
            ),
            'metrics' => array(
                array(
                    'tags' => array(
                        'host' => 'precise64'
                    ),
                    'name' => 'kairosdb.protocol.http_request_count'
                )
            )
        );

        return $this->post('datapoints/query/tags', $data);
    }

    /**
     * @return string
     */
    public function deleteDataPoints()
    {
        $data = array(
            'metrics' => array(
                array(
                    'tags' => array(
                        'host' => 'precise64'
                    ),
                    'name' => 'kairosdb.protocol.http_request_count'
                )
            ),
            'cache_time' => 0,
            'start_relative' => array(
                'value' => '1',
                'unit' => 'hours'
            )
        );

        return $this->post('datapoints/delete', $data);
    }

    /**
     * @param string $metricName
     * @return bool
     */
    public function deleteMetric($metricName)
    {
        return $this->delete(sprintf('metric/%s', $metricName));
    }

    /**
     * @return string
     */
    public function getMetricNames()
    {
        return $this->get('metricnames');
    }

    /**
     * @return string
     */
    public function getTagNames()
    {
        return $this->get('tagnames');
    }

    /**
     * @return string
     */
    public function getTagValues()
    {
        return $this->get('tagvalues');
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->get('version');
    }

    /**
     * @param string $uri
     * @return string
     */
    private function get($uri)
    {
        $response = $this->httpClient->get($uri)->send();

        return $response->json();
    }

    /**
     * @param string $uri
     * @param array $data
     * @return string
     */
    private function post($uri, array $data = array())
    {
        $response = $this->httpClient->post(
            $uri,
            array(
                'Content-Type' => 'application/json'
            ),
            json_encode($data, true)
        )->send();

        return $response->json();
    }

    /**
     * @param string $uri
     * @return bool
     */
    private function delete($uri)
    {
        try {

            $this->httpClient->delete($uri)->send();

        } catch (Exception $e) {

            echo $e->getMessage();
            return false;
        }

        return true;
    }
}