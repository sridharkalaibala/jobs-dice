<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Dice extends AbstractProvider
{
    /**
     * Map of setter methods to query parameters
     *
     * @var array
     */
    protected $queryMap = [
        'setDirect' => 'direct',
        'setAreacode' => 'areacode',
        'setCountry' => 'country',
        'setState' => 'state',
        'setSkill' => 'skill',
        'setCity' => 'city',
        'setText' => 'text',
        'setIp' => 'ip',
        'setAge' => 'age',
        'setDiceid' => 'diceid',
        'setPage' => 'page',
        'setPgcnt' => 'pgcnt',
        'setSort' => 'sort',
        'setSd' => 'sd',
    ];

    /**
     * Current api query parameters
     *
     * @var array
     */
    protected $queryParams = [
        'direct' => null,
        'areacode' => null,
        'country' => null,
        'state' => null,
        'skill' => null,
        'city' => null,
        'text' => null,
        'ip' => null,
        'age' => null,
        'diceid' => null,
        'page' => null,
        'pgcnt' => null,
        'sort' => null,
        'sd' => null,
    ];

    /**
     * Create new Dice jobs client.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        array_walk($parameters, [$this, 'updateQuery']);
    }

    /**
     * Magic method to handle get and set methods for properties
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->queryMap[$method], $parameters[0])) {
            $this->updateQuery($parameters[0], $this->queryMap[$method]);
        }
        return parent::__call($method, $parameters);
    }

    /**
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $defaults = [
            'jobTitle',
            'company',
            'location',
            'date',
            'detailUrl'
        ];

        $payload = static::parseAttributeDefaults($payload, $defaults);

        $job = new Job([
            'title' => $payload['jobTitle'],
            'name' => $payload['jobTitle'],
            'url' => $payload['detailUrl'],
            'location' => $payload['location'],
        ]);

        $location = static::parseLocation($payload['location']);

        $job->setCompany($payload['company'])
            ->setDatePostedAsString($payload['date']);

        if (isset($location[0])) {
            $job->setCity($location[0]);
        }
        if (isset($location[1])) {
            $job->setState($location[1]);
        }

        return $job;
    }

    /**
     * Get data format
     *
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * Get listings path
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'resultItemList';
    }

    /**
     * Get query string for client based on properties
     *
     * @return string
     */
    public function getQueryString()
    {
        return http_build_query($this->queryParams);
    }

    /**
     * Get url
     *
     * @return  string
     */
    public function getUrl()
    {
        $query_string = $this->getQueryString();

        return 'http://service.dice.com/api/rest/jobsearch/v1/simple.json?'.$query_string;
    }

    /**
     * Get http verb
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }

    /**
     * Attempts to update current query parameters.
     *
     * @param  string  $value
     * @param  string  $key
     *
     * @return Careerbuilder
     */
    protected function updateQuery($value, $key)
    {
        if (array_key_exists($key, $this->queryParams)) {
            $this->queryParams[$key] = $value;
        }
        return $this;
    }
}
