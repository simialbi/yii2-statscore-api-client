<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore;

use simialbi\yii2\statscore\models\Area;
use simialbi\yii2\statscore\models\Competition;
use simialbi\yii2\statscore\models\Season;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

/**
 * Class Client
 * @package simialbi\yii2\statscore
 *
 * @property-read string $token
 */
class Client extends Component
{
    /**
     * @var string url API endpoint
     */
    public $baseUrl = 'https://api.softnetsport.com/v2/';

    /**
     * @var integer Client identifier
     */
    public $clientId;

    /**
     * @var string Secret key
     */
    public $secretKey;

    /**
     * @var string Username (for AMQP usage)
     */
    public $username;

    /**
     * @var string Determines language for the output data. If not set, app language will be used
     */
    public $language;

    /**
     * @var boolean|integer Set a duration in seconds before a cache entry will expire. If set, data will
     * be cached by this duration. If not, there will be no caching.
     */
    public $cacheDuration = false;

    /**
     * @var \yii\httpclient\Client Http client to send and parse requests
     */
    private $_client;

    /**
     * @var string Valid token generated during authentication process
     */
    private $_token;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (empty($this->baseUrl)) {
            throw new InvalidConfigException("The 'baseUrl' cannot be empty.");
        }
        if (empty($this->clientId) || empty($this->secretKey)) {
            throw new InvalidConfigException("The 'clientId' and 'secretKey' parameters cannot be empty.");
        }
        if (empty($this->language)) {
            $this->language = (false !== ($pos = strpos(Yii::$app->language, '-')))
                ? substr(Yii::$app->language, 0, $pos)
                : Yii::$app->language;
        }

        $this->_client = new \yii\httpclient\Client([
            'baseUrl' => $this->baseUrl,
            'requestConfig' => [
                'format' => \yii\httpclient\Client::FORMAT_JSON
            ],
            'responseConfig' => [
                'format' => \yii\httpclient\Client::FORMAT_JSON
            ]
        ]);
        ArrayHelper::setValue($this->_client->requestConfig, 'data.token', $this->token);
        ArrayHelper::setValue($this->_client->requestConfig, 'data.lang', $this->language);
    }

    /**
     * Get method for private token var
     *
     * @return string Valid token generated during authentication process
     * @throws HttpException
     */
    public function getToken()
    {
        if ($this->_token) {
            return $this->_token;
        }
        if (Yii::$app->session && Yii::$app->session->has('statscoreToken')) {
            $this->_token = Yii::$app->session->get('statscoreToken');

            return $this->_token;
        }

        $data = $this->request('oauth', [
            'client_id' => $this->clientId,
            'secret_key' => $this->secretKey
        ]);
        $this->_token = ArrayHelper::getValue($data, 'token');

        if (Yii::$app->session) {
            Yii::$app->session->set('statscoreToken', $this->_token);
        }

        return $this->_token;
    }

    /**
     * Returns a list of all available areas (continents and countries)
     *
     * @param integer|null $parent_area_id
     * @return Area[]
     * @throws HttpException
     */
    public function getAreas($parent_area_id = null)
    {
        $data = $this->request('areas', [
            'parent_area_id' => $parent_area_id
        ]);

        $areas = [];
        foreach (ArrayHelper::getValue($data, 'areas', []) as $area) {
            $model = new Area($area);
            $areas = $model;
        }

        return $areas;
    }

    /**
     * Returns a list of all available competitions
     *
     * @param null|string $area_type Determines type of area in which competitions are played.
     * @param null|string $type Determines type competition.
     * @param null|integer|integer $area_id Determines the area in which competition are played.
     * @param null|integer $sport_id Determines the sport identificator in which the competitions are played.
     * @param null|integer $tour_id Determines the tour identificator in which the competitions are played.
     * @param null|array|string $multi_ids List of competition identifiers.
     * @param null|string $gender Determines competition gender. Allows selection of e.g. only
     * WTA Womens Competitions in Tennis.
     * @param null|string|integer $timestamp Selection date, format UNIX_TIMESTAMP. Only changes in competitions
     * that occurred or were updated after this timestamp will be returned
     * @param null|string $short_name Determines competition short_name. The attribute must have minimum 3 characters.
     * @param null|string $short_type Determines sort type for competitions (internal usage only)
     * @param null|string|integer $date_from Selection datetime
     * @param null|string|integer $date_to Selection datetime
     * @param null|string $status_type Return only competition with event status
     * live,finished,scheduled,other,cancelled,interrupted,deleted.
     * @param null|string $tz Custom timezone for data_from and date_to.
     * @return Competition[]
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function getCompetitions(
        $area_type = null,
        $type = null,
        $area_id = null,
        $sport_id = null,
        $tour_id = null,
        $multi_ids = null,
        $gender = null,
        $timestamp = null,
        $short_name = null,
        $short_type = null,
        $date_from = null,
        $date_to = null,
        $status_type = null,
        $tz = null
    ) {
        if (is_array($multi_ids)) {
            $multi_ids = implode(',', $multi_ids);
        }
        $data = $this->request('competitions', [
            'area_type' => $area_type,
            'type' => $type,
            'area_id' => $area_id,
            'sport_id' => $sport_id,
            'tour_id' => $tour_id,
            'multi_ids' => $multi_ids,
            'gender' => $gender,
            'timestamp' => $timestamp,
            'short_name' => $short_name,
            'short_type' => $short_type,
            'date_from' => Yii::$app->formatter->asDatetime($date_from, 'yyyy-MM-dd HH:mm:ss'),
            'date_to' => Yii::$app->formatter->asDatetime($date_to, 'yyyy-MM-dd HH:mm:ss'),
            'status_type' => $status_type,
            'tz' => $tz
        ]);

        $competitions = [];
        foreach (ArrayHelper::getValue($data, 'competitions', []) as $competition) {
            $model = new Competition($competition);
            $competitions = $model;
        }

        return $competitions;
    }

    /**
     * Returns data for a single competition
     *
     * @param integer $competition_id The requested competition identifier
     *
     * @return Competition
     * @throws HttpException
     */
    public function getCompetition($competition_id)
    {
        $data = $this->request('competitions/'.$competition_id);

        $competition = new Competition(ArrayHelper::getValue($data, 'competition'));
        $competition->seasons = [];
        $seasons = ArrayHelper::getValue($data, 'competition.seasons', []);
        foreach ($seasons as $season) {
            $competition->seasons[] = new Season($season);
        }

        return $competition;
    }

    /**
     * Internal method for all api request
     *
     * @param string $endpoint api resource endpoint to call
     * @param array $data Content data fields.
     * @return array Result data
     * @throws HttpException
     */
    protected function request($endpoint, $data = [])
    {
        if ($this->cacheDuration) {
            $key = ArrayHelper::merge($data, ['endpoint' => $endpoint]);
            ksort($key);

            if (Yii::$app->cache->exists($key)) {
                return Yii::$app->cache->get($key);
            }
        }

        $request = $this->_client
            ->createRequest()
            ->setMethod('get')
            ->setUrl($endpoint)
            ->setData($data);
        $response = $request->send();
        /* @var $response \yii\httpclient\Response */
        $api = ArrayHelper::remove($response->data, 'api', []);

        if (!$response->isOk) {
            $error = ArrayHelper::remove($api, 'error', [
                'message' => 'Unknown error',
                'code' => $response->statusCode
            ]);

            throw new HttpException($error['code'], $error['message']);
        }

        $resultData = ArrayHelper::getValue($api, 'data');
        $nextPage = ArrayHelper::getValue($api, 'method.next_page');
        $pageParam = ArrayHelper::getValue($data, 'page');

        if (!empty($nextPage) && is_numeric($nextPage)) {
            ArrayHelper::setValue($data, 'page', $nextPage);
            $subData = $this->request($endpoint, $data);

            $resultData = ArrayHelper::merge($resultData, $subData);
        }

        if ($this->cacheDuration && null === $pageParam) {
            Yii::$app->cache->set($key, $resultData);
        }

        return $resultData;
    }
}