<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore;

use simialbi\yii2\statscore\models\Area;
use simialbi\yii2\statscore\models\Column;
use simialbi\yii2\statscore\models\Competition;
use simialbi\yii2\statscore\models\Correction;
use simialbi\yii2\statscore\models\Detail;
use simialbi\yii2\statscore\models\Event;
use simialbi\yii2\statscore\models\Group;
use simialbi\yii2\statscore\models\Incident;
use simialbi\yii2\statscore\models\Lineup;
use simialbi\yii2\statscore\models\Participant;
use simialbi\yii2\statscore\models\participant\Detail as ParticipantDetail;
use simialbi\yii2\statscore\models\Result;
use simialbi\yii2\statscore\models\Round;
use simialbi\yii2\statscore\models\Season;
use simialbi\yii2\statscore\models\Sport;
use simialbi\yii2\statscore\models\sport\Detail as SportDetail;
use simialbi\yii2\statscore\models\sport\Incident as SportIncident;
use simialbi\yii2\statscore\models\Stage;
use simialbi\yii2\statscore\models\Standing;
use simialbi\yii2\statscore\models\StandingType;
use simialbi\yii2\statscore\models\Stat;
use simialbi\yii2\statscore\models\Status;
use simialbi\yii2\statscore\models\Tour;
use simialbi\yii2\statscore\models\Zone;
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
        if (Yii::$app->has('session') && Yii::$app->session->has('statscoreToken')) {
            $this->_token = Yii::$app->session->get('statscoreToken');

            return $this->_token;
        }

        $data = $this->request('oauth', [
            'client_id' => $this->clientId,
            'secret_key' => $this->secretKey
        ]);
        $this->_token = ArrayHelper::getValue($data, 'token');

        if (Yii::$app->has('session')) {
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
     * @param array $requestData additional query data filters
     * @return Competition[]
     * @throws HttpException
     */
    public function getCompetitions(array $requestData = [])
    {
        $data = $this->request('competitions', $requestData);

        $competitions = [];
        foreach (ArrayHelper::getValue($data, 'competitions', []) as $competition) {
            $model = new Competition($competition);
            $competitions[] = $model;
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
        $data = $this->request('competitions/' . $competition_id);

        $competitionData = ArrayHelper::getValue($data, 'competition');
        $seasons = ArrayHelper::remove($competitionData, 'seasons', []);
        $competition = new Competition($competitionData);
        foreach ($seasons as $season) {
            $competition->seasons[] = new Season($season);
        }

        return $competition;
    }

    /**
     * Returns a single event with details including participants, partial results, stats, lineups and important
     * incidents for the event
     *
     * @param array $requestData additional query data filters
     * @return Competition[]
     * @throws HttpException
     */
    public function getEvents(array $requestData = [])
    {
        $data = $this->request('events', $requestData);

        $competitions = [];
        foreach (ArrayHelper::getValue($data, 'competitions', []) as $c) {
            $competition = $this->buildCompetition($c);
            $competitions[] = $competition;
        }

        return $competitions;
    }

    /**
     * Returns a single event with details including participants, partial results, stats,
     * lineups and important incidents for the event
     *
     * @param integer $event_id The requested event identifier
     * @return Competition
     * @throws HttpException
     */
    public function getEvent($event_id)
    {
        $data = $this->request('events/' . $event_id);

        $competition = $this->buildCompetition(ArrayHelper::getValue($data, 'competition'));

        return $competition;
    }

    /**
     * Returns a list of all available groups that played in the selected stage
     *
     * @param integer $stage_id Identifier of the stage related to the group.
     * @param array $requestData additional query data filters
     * @return Competition
     * @throws HttpException
     */
    public function getGroups($stage_id, array $requestData = [])
    {
        $requestData['stage_id'] = $stage_id;
        $data = $this->request('groups', $requestData);

        $competition = $this->buildCompetition(ArrayHelper::getValue($data, 'competition'));

        return $competition;
    }

    /**
     * Returns incidents, which may occur during the event (list of available incidents)
     *
     * @param array $requestData additional query data filters
     * @return Incident[]
     * @throws HttpException
     */
    public function getIncidents(array $requestData = [])
    {
        $data = $this->request('incidents', $requestData);

        $incidents = [];
        foreach (ArrayHelper::getValue($data, 'incidents', []) as $i) {
            $incident = new Incident($i);
            $incidents[] = $incident;
        }

        return $incidents;
    }

    /**
     * Returns LIVE events related to competitions, seasons, stages and groups
     *
     * @param array $requestData additional query data filters
     * @return array
     * @throws HttpException
     */
    public function getLiveScore(array $requestData = [])
    {
        $data = $this->request('livescore', $requestData);

        $competitions = [];
        foreach (ArrayHelper::getValue($data, 'competitions', []) as $c) {
            $competition = $this->buildCompetition($c);
            $competitions[] = $competition;
        }

        return $competitions;
    }

    /**
     * Returns a list of all available participants (teams or persons) for all sports
     *
     * @param integer $sport_id Identifier for the sport. Allows you to filter participants for the selected sport.
     * @param array $requestData additional query data filters
     * @return Participant[]
     * @throws HttpException
     */
    public function getParticipants($sport_id, array $requestData = [])
    {
        $requestData['sport_id'] = $sport_id;
        $data = $this->request('participants', $requestData);

        $participants = [];
        foreach (ArrayHelper::getValue($data, 'participants', []) as $p) {
            $details = ArrayHelper::remove($p, 'details');

            $participant = new Participant($p);
            if (!empty($details)) {
                $participant->details = new ParticipantDetail($details);
            }

            $participants[] = $participant;
        }

        return $participants;
    }

    /**
     * Returns a list of personnel related to selected team
     *
     * @param integer $participant_id The requested participant (team) identifier
     * @param array $requestData additional query data filters
     * @return Participant[]
     * @throws HttpException
     */
    public function getParticipantsSquad($participant_id, array $requestData = [])
    {
        $data = $this->request('participants/' . $participant_id . '/squad', $requestData);

        $participants = [];
        foreach (ArrayHelper::getValue($data, 'participants', []) as $p) {
            $details = ArrayHelper::remove($p, 'details', []);

            $participant = new Participant($p);

            foreach ($details as $d) {
                $detail = new ParticipantDetail($d);

                $participant->details[] = $detail;
            }

            $participants[] = $participant;
        }

        return $participants;
    }

    /**
     * Returns a list of all available rounds which could be related to the event f.e Round 1, Quarterfinals etc.
     *
     * @param array $requestData additional query data filters
     * @return Round[]
     * @throws HttpException
     */
    public function getRounds(array $requestData = [])
    {
        $data = $this->request('rounds', $requestData);

        $rounds = [];
        foreach (ArrayHelper::getValue($data, 'rounds', []) as $r) {
            $round = new Round($r);

            $rounds[] = $round;
        }

        return $rounds;
    }

    /**
     * Returns a list of all available seasons played in the competitions
     *
     * @param array $requestData additional query data filters
     * @return Competition[]
     * @throws HttpException
     */
    public function getSeasons(array $requestData = [])
    {
        $data = $this->request('seasons', $requestData);

        $competitions = [];
        foreach (ArrayHelper::getValue($data, 'competitions', []) as $c) {
            $competition = $this->buildCompetition($c);
            $competitions[] = $competition;
        }

        return $competitions;
    }

    /**
     * Returns the seasons played in the competitions
     *
     * @param integer $season_id The requested season identifier
     * @param array $requestData additional query data filters
     * @return Competition
     * @throws HttpException
     */
    public function getSeason($season_id, array $requestData = [])
    {
        $data = $this->request('seasons/' . $season_id, $requestData);

        $competition = $this->buildCompetition(ArrayHelper::getValue($data, 'competition', []));

        return $competition;
    }

    /**
     * Returns a list of all available sports
     *
     * @param array $requestData additional query data filters
     * @return Sport[]
     * @throws HttpException
     */
    public function getSports(array $requestData = [])
    {
        $data = $this->request('sports', $requestData);

        $sports = [];
        foreach (ArrayHelper::getValue($data, 'sports', []) as $s) {
            $sport = new Sport($s);

            $sports[] = $sport;
        }

        return $sports;
    }

    /**
     * Returns information including statuses, result types, details and statistics available for the requested sport
     *
     * @param integer $sport_id The requested sport identifier
     * @param array $requestData additional query data filters
     * @return Sport
     * @throws HttpException
     */
    public function getSport($sport_id, array $requestData = [])
    {
        $data = $this->request('sports/' . $sport_id, $requestData);

        $s = ArrayHelper::getValue($data, 'sport', []);
        $statuses = ArrayHelper::remove($s, 'statuses', []);
        $results = ArrayHelper::remove($s, 'results', []);
        $stats = ArrayHelper::remove($s, 'stats', []);
        $details = ArrayHelper::remove($s, 'details', []);
        $incidents = ArrayHelper::remove($s, 'incidents', []);
        $standingTypes = ArrayHelper::remove($s, 'standing_types', []);
        $venuesDetails = ArrayHelper::remove($s, 'venues_details', []);
        $sport = new Sport($s);

        foreach ($statuses as $st) {
            $status = new Status($st);

            $sport->statuses[] = $status;
        }
        foreach ($results as $r) {
            $result = new Result($r);

            $sport->results[] = $result;
        }
        foreach (ArrayHelper::getValue($stats, 'team', []) as $teamStat) {
            $stat = new Stat($teamStat);

            $sport->stats[] = $stat;
        }
        foreach (ArrayHelper::getValue($stats, 'person', []) as $personStat) {
            $stat = new Stat($personStat);

            $sport->stats[] = $stat;
        }
        foreach ($details as $d) {
            $detail = new SportDetail($d);

            $sport->details[] = $detail;
        }
        foreach ($incidents as $i) {
            $incident = new SportIncident($i);

            $sport->incidents[] = $incident;
        }
        foreach ($standingTypes as $type) {
            $columns = ArrayHelper::remove($type, 'columns', []);

            $standingType = new StandingType($type);
            foreach ($columns as $c) {
                $column = new Column($c);

                $standingType->columns[] = $column;
            }

            $sport->standing_types[] = $standingType;
        }
        foreach ($venuesDetails as $venuesDetail) {
            $detail = new Detail($venuesDetail);

            $sport->venues_details[] = $detail;
        }

        return $sport;
    }

    /**
     * Returns a list of all available stages played in a particular season
     *
     * @param integer $season_id Determines season to which stages belongs
     * @param array $requestData additional query data filters
     * @return Competition
     * @throws HttpException
     */
    public function getStages($season_id, array $requestData = [])
    {
        $requestData['season_id'] = $season_id;
        $data = $this->request('stages', $requestData);

        $competition = $this->buildCompetition(ArrayHelper::getValue($data, 'competition', []));

        return $competition;
    }

    /**
     * Returns the single standings data
     *
     * @param array $requestData additional query data filters
     * @return Standing[]
     * @throws HttpException
     */
    public function getStandings(array $requestData = [])
    {
        $data = $this->request('standings', $requestData);

        $standings = [];
        foreach (ArrayHelper::getValue($data, 'standings_list', []) as $item) {
            $standing = new Standing($item);

            $standings[] = $standing;
        }

        return $standings;
    }

    /**
     * Returns single standings data
     *
     * @param integer $standing_id The requested standing identifier
     * @param array $requestData additional query data filters
     * @return Standing
     * @throws HttpException
     */
    public function getStanding($standing_id, array $requestData = [])
    {
        $data = $this->request('standings/' . $standing_id, $requestData);

        $s = ArrayHelper::getValue($data, 'standing', []);
        $groups = ArrayHelper::remove($s, 'groups', []);
        $standing = new Standing($s);

        foreach ($groups as $g) {
            $participants = ArrayHelper::remove($g, 'participants', []);
            $corrections = ArrayHelper::remove($g, 'corrections', []);
            $zones = ArrayHelper::remove($g, 'zones', []);

            $group = new Group($g);

            foreach ($participants as $p) {
                $columns = ArrayHelper::remove($p, 'columns', []);

                $participant = new Participant($p);
                foreach ($columns as $c) {
                    $column = new Column($c);

                    $participant->columns[] = $column;
                }

                $group->participants[] = $participant;
            }
            foreach ($corrections as $c) {
                $correction = new Correction($c);

                $group->corrections[] = $correction;
            }
            foreach ($zones as $z) {
                $zone = new Zone($z);

                $group->zones[] = $zone;
            }

            $standing->groups[] = $group;
        }

        return $standing;
    }

    /**
     * Returns a list of all available statuses for events from all sports
     *
     * @param integer $sport_id Identifier of the sport. Allows the filter status for selected sport
     * @param array $requestData additional query data filters
     * @return Status[]
     * @throws HttpException
     */
    public function getStatuses($sport_id, array $requestData = [])
    {
        $requestData['sport_id'] = $sport_id;
        $data = $this->request('statuses', $requestData);

        $statuses = [];
        foreach (ArrayHelper::getValue($data, 'statuses', []) as $s) {
            $status = new Status($s);

            $statuses[] = $status;
        }

        return $statuses;
    }

    /**
     * Returns a list of all available tours related to the competitions e.g. ATP Tour, WTA Tour
     *
     * @param array $requestData additional query data filters
     * @return Tour[]
     * @throws HttpException
     */
    public function getTours(array $requestData = [])
    {
        $data = $this->request('tours', $requestData);

        $tours = [];
        foreach (ArrayHelper::getValue($data, 'tours', []) as $t) {
            $tour = new Tour($t);

            $tours[] = $tour;
        }

        return $tours;
    }

    /**
     * Internal method for all api request
     *
     * @param string $endpoint api resource endpoint to call
     * @param array $data Content data fields.
     * @return array Result data
     * @throws HttpException
     */
    protected function request($endpoint, array $data = [])
    {
        if ($this->cacheDuration && strcasecmp($endpoint, 'livescore') !== 0) {
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
            ->addData($data);
        $response = $request->send();
        /* @var $response \yii\httpclient\Response */
        $api = ArrayHelper::getValue($response->data, 'api', []);

        if (!$response->isOk) {
            $error = ArrayHelper::remove($api, 'error', [
                'message' => 'Unknown error',
                'code' => $response->statusCode
            ]);

            throw new HttpException(
                ArrayHelper::getValue($error, 'code', 500),
                ArrayHelper::getValue($error, 'message', 'Unknown error')
            );
        }

        $resultData = ArrayHelper::getValue($api, 'data');
        $nextPage = ArrayHelper::getValue($api, 'method.next_page');
        $pageParam = ArrayHelper::getValue($data, 'page');

        if (!empty($nextPage)) {
            $params = [];
            parse_str(parse_url($nextPage, PHP_URL_QUERY), $params);
            ArrayHelper::setValue($data, 'page', $params['page']);
            $subData = $this->request($endpoint, $data);

            $resultData = ArrayHelper::merge($resultData, $subData);
        }

        if ($this->cacheDuration && null === $pageParam) {
            Yii::$app->cache->set($key, $resultData);
        }

        return $resultData;
    }

    /**
     * Build competition with all possible children out of data array
     *
     * @param array $c Competition array data
     * @return Competition
     */
    protected function buildCompetition(array $c)
    {
        $currSeason = ArrayHelper::remove($c, 'season');
        $seasons = ArrayHelper::remove($c, 'seasons', []);

        $competition = new Competition($c);
        if ($currSeason) {
            $seasons[] = $currSeason;
        }
        foreach ($seasons as $s) {
            $stages = ArrayHelper::remove($s, 'stages', []);

            $season = new Season($s);
            foreach ($stages as $st) {
                $groups = ArrayHelper::remove($st, 'groups', []);

                $stage = new Stage($st);
                foreach ($groups as $g) {
                    $events = ArrayHelper::remove($g, 'events', []);

                    $group = new Group($g);
                    foreach ($events as $e) {
                        $details = ArrayHelper::remove($e, 'details', []);
                        $participants = ArrayHelper::remove($e, 'participants', []);
                        $incidents = ArrayHelper::remove($e, 'event_incidents', []);

                        $event = new Event($e);
                        foreach ($details as $d) {
                            $detail = new Detail($d);

                            $event->details[] = $detail;
                        }
                        foreach ($participants as $p) {
                            $results = ArrayHelper::remove($p, 'results', []);
                            $stats = ArrayHelper::remove($p, 'stats', []);
                            $lineups = ArrayHelper::remove($p, 'lineups', []);

                            $participant = new Participant($p);
                            foreach ($results as $r) {
                                $result = new Result($r);

                                $participant->results[] = $result;
                            }
                            foreach ($stats as $sta) {
                                $stat = new Stat($sta);

                                $participant->stats[] = $stat;
                            }
                            foreach ($lineups as $l) {
                                $lineup = new Lineup($l);

                                $participant->lineups[] = $lineup;
                            }

                            $event->participants[] = $participant;
                        }
                        foreach ($incidents as $i) {
                            $incident = new Incident($i);

                            $event->incidents[] = $incident;
                        }

                        $group->events[] = $event;
                    }

                    $stage->groups[] = $group;
                }

                $season->stages[] = $stage;
            }


            $competition->seasons[] = $season;
        }

        return $competition;
    }
}