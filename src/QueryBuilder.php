<?php

namespace KairosDB;

/**
 * Class QueryBuilder
 * @package KairosDB
 */
class QueryBuilder
{
    /**
     * @var array
     */
    private $query = array();

    /**
     * @var array
     */
    private $currentMetric = array();

    /**
     * @var array
     */
    private $metrics = array();

    /**
     * @param string $metricName
     * @return $this
     */
    public function addMetric($metricName)
    {
        if ($this->currentMetric) {
            $this->metrics[] = $this->currentMetric;
            $this->currentMetric = array();
        }

        $this->currentMetric['name'] = $metricName;

        return $this;
    }

    /**
     * @param string|int $value
     * @return $this
     */
    public function groupByValue($value)
    {
        $this->currentMetric['group_by'] = array(
            'name' => 'value',
            'range_size' => $value
        );

        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function groupByTags(array $tags)
    {
        $this->currentMetric['group_by'] = array(
            'name' => 'tag',
            'tags' => $tags
        );

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->currentMetric['limit'] = $limit;

        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function tags(array $tags)
    {
        $this->currentMetric['tags'] = $tags;

        return $this;
    }

    /**
     * Can be :
     * - absolute: in miliseconds
     * - relative: array ['value'=> 1, 'unit'=>'days']
     *
     * @param mixed $start
     * @return $this
     */
    public function start($start)
    {
        $this->setTimeLimits('start', $start);

        return $this;
    }

    /**
     * Can be :
     * - absolute: in miliseconds
     * - relative: array ['value'=> 1, 'unit'=>'days']
     *
     * @param mixed $end
     * @return $this
     */
    public function end($end)
    {
        $this->setTimeLimits('end', $end);

        return $this;
    }

    /**
     * The amount of time in seconds to cache the output of the query.
     * @param int $seconds
     * @return $this
     */
    public function cache($seconds)
    {
        $this->query['cache_time'] = $seconds;
        return $this;
    }

    /**
     * @return array $query
     */
    public function build()
    {
        $this->metrics[] = $this->currentMetric;
        $this->query['metrics'] = $this->metrics;

        return $this->query;
    }


    /**
     * TODO: throw exceptions if unit/value have not been specified
     *
     * @param int|string $type
     * @param int|array $limits
     */
    private function setTimeLimits($type, $limits)
    {
        if (is_array($limits)) {

            $this->query["{$type}_relative"]= array(
                'unit'  => $limits['unit'],
                'value' => $limits['value']
            );

        } else if(is_numeric($limits)) {
            $this->query["{$type}_absolute"] = $limits;
        }
    }
} 