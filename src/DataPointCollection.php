<?php

namespace KairosDB;

/**
 * Class DataPointCollection
 *
 * @package KairosDB
 */
class DataPointCollection
{
    /**
     * @var array
     */
    private $points = array();

    /**
     * @var array
     */
    private $tags = array();

    /**
     * @var string
     */
    private $metricName;

    /**
     * @param string $metricName
     * @param array $tags
     */
    public function __construct($metricName, array $tags)
    {
        $this->metricName = $metricName;
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->metricName,
            'tags' => $this->tags,
            'datapoints' => $this->points
        );
    }

    /**
     * @param mixed $value
     * @param null $timestamp
     */
    public function addPoint($value, $timestamp = null)
    {
        $timestamp = is_null($timestamp) ? round(microtime(true) * 1000) : $timestamp;
        $this->points[] = array($timestamp, $value);
    }

    /**
     * @return string
     */
    public function getMetricName()
    {
        return $this->metricName;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

} 