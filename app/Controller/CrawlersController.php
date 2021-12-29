<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');

class CrawlersController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Crawlers';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	var $components = array("Cookie", "RequestHandler");

	var $helpers = array("Html", "Form");

    public function importProfile() {
        $this->autoRender = false;

        $default = ini_get('max_execution_time');
        set_time_limit(1000000);
        ini_set("memory_limit", "256M");

        $this->AthleteScore = ClassRegistry::init("AthleteScore");
        $this->Athlete = ClassRegistry::init("Athlete");

        $url = "";
        if(isset($this->params['url']['u'])) $url = $this->params['url']['u'];

        if($url) {
            $start = strpos($url, "/athlete/") + 9;
            if($start !== false) {
                $end = strlen($url);
                if($end !== false) {
                    $id = substr($url, $start, $end - $start);

                    $athlete = $this->Athlete->findByGid($id);
                    if(!$athlete) {
                        $user = $this->parseUserDetails($url);

                        if($user) {
                            $this->Athlete->create();
                            $this->Athlete->save(array("name" => $user['name'], "region" => $user['region'], "region_url" => $user['region_url'], "affiliate" => $user['affiliate'],
                                "affiliate_url" => $user['affiliate_url'], "gender" => $user['gender'], "age" => $user['age'], "height" => $user['height'], "weight" => $user['weight'], "gid" => $id, "image" => $user['image']));

                            $this->AthleteScore->create();
                            $this->AthleteScore->save(array("athlete_id" => $this->Athlete->id, "wod1" => $user['scores']['wod1']['score'],
                                "wod2" => $user['scores']['wod2']['score'], "wod3" => $user['scores']['wod3']['score'], "wod4" => $user['scores']['wod4']['score'],
                                "wod5" => $user['scores']['wod5']['score']));

                            $retval = array("result" => "SUCCESS", "id" => $this->Athlete->id);
                        }
                        else {
                            $retval = array("result" => "FAILURE", "message" => "Could parse user profile. Make sure your URL is correct.");
                        }
                    }
                    else {
                        $retval = array("result" => "FAILURE", "message" => "This athlete already exists.");
                    }
                }
                else {
                    $retval = array("result" => "FAILURE", "message" => "Not a valid URL");
                }
            }
            else {
                $retval = array("result" => "FAILURE", "message" => "Not a valid URL");
            }
        }
        else {
            $retval = array("result" => "FAILURE", "message" => "Missing valid URL");
        }

        echo json_encode($retval);
    }

	public function recrawlAthletes() {
        $this->autoRender = false;

		$default = ini_get('max_execution_time');
        set_time_limit(1000000);
        ini_set("memory_limit", "256M");

        $this->AthleteScore = ClassRegistry::init("AthleteScore");
        $this->Athlete = ClassRegistry::init("Athlete");
        $this->UsersAthlete = ClassRegistry::init("UsersAthlete");
        $this->EmailQueue = ClassRegistry::init("EmailQueue");

        $stage = 5;
        $limit = 5000;

        while(true) {
            for($division = 1; $division <= 13; $division++) {
                if($division == 11) continue;

                $seen = array();
                for($page = 1; $page <= 10; $page++) {
                    $site_url = "http://games.crossfit.com/scores/leaderboard.php?stage=".$stage."&sort=0&division=".$division."&region=0&numberperpage=".$limit."&page=".$page."&competition=0&frontpage=0&expanded=0&full=1&year=13&showtoggles=0&hidedropdowns=0&showathleteac=1&athletename=";

                    $html = $this->getResponse($site_url);
                    if(strpos($html, "No scores yet") > 0) {
                        $page = 11;
                        continue;
                    }

                    $count = 0;

                    $start = strpos($html, "<td class=\"name\">");
                    while($start !== false) {
                        $html = substr($html, $start);

                        $start = strpos($html, "href") + 6;
                        $end = strpos($html, "\"", $start);

                        $url = substr($html, $start, $end - $start);

                        $start = strpos($html, "/athlete/", $start) + 9;
                        $end = strpos($html, "\"", $start);

                        $id = substr($html, $start, $end - $start);

                        $start = strpos($html, "_top", $end) + 6;
                        $end = strpos($html, "</a>", $start);
                        
                        $name = substr($html, $start, $end - $start);

                        if(isset($seen[$id]) || trim($id) == "") continue;

                        $seen[$id] = true;

                        $athlete = $this->Athlete->findByGid($id);
                        if($athlete) {
                            $scores = $this->AthleteScore->findByAthleteId($athlete['Athlete']['id']);
                            $result = $this->getScores($html, $end);
                            
                            if(!isset($result['wod1'])) continue;
                            
                            if($scores && (!$scores['AthleteScore']['wod'.$stage] || $scores['AthleteScore']['wod'.$stage] != $result['wod'.$stage]['score'])) {
                                if(!$scores['AthleteScore']['wod'.$stage]) {
                                    $update = 0;
                                }
                                else {
                                    $update = 1;
                                }

                                $scores['AthleteScore']['wod'.$stage] = $result['wod'.$stage]['score'];

                                $this->AthleteScore->save($scores);

                                // someone has alerts for this athlete
                                if($this->UsersAthlete->find("first", array("conditions" => array("athlete_id" => $athlete['Athlete']['id'])))) 
                                {
                                    $this->EmailQueue->create();
                                    if($result['wod'.$stage]['score']) {
                                        $rank = trim($result['wod'.$stage]['rank']);
                                        if(!$rank || $rank == null) $rank = 0;
                                        
                                        $this->EmailQueue->save(array("athlete_id" => $athlete['Athlete']['id'], "rank" => $rank, "is_update" => $update, "workout_number" => $stage, "score" => $result['wod'.$stage]['score'], "sent" => 0, "created_date" => date("Y-m-d H:i:s")));
                                    }
                                    
                                }
                            }
                        }
                        else { // user not found, so create this record
                            echo "creating record: ".$id. " " .$name . " " . $url. "\n";

                            $user = $this->parseUserDetails($url);

                            $this->Athlete->create();
                            $this->Athlete->save(array("name" => $name, "region" => $user['region'], "region_url" => $user['region_url'], "affiliate" => $user['affiliate'],
                                "affiliate_url" => $user['affiliate_url'], "gender" => $user['gender'], "age" => $user['age'], "height" => $user['height'], "weight" => $user['weight'], "gid" => $id, "image" => $user['image']));

                            if(!isset($user['scores'])) continue;

                            $this->AthleteScore->create();
                            $this->AthleteScore->save(array("athlete_id" => $this->Athlete->id, "wod1" => $user['scores']['wod1']['score'],
                                "wod2" => $user['scores']['wod2']['score'], "wod3" => $user['scores']['wod3']['score'], "wod4" => $user['scores']['wod4']['score'],
                                "wod5" => $user['scores']['wod5']['score']));
                        }

                        $start = strpos($html, "<td class=\"name\">", $start);
                        $count++;
                    }
                }
            }
        }        
	}

	public function baselineCrawl() {
        $default = ini_get('max_execution_time');
        set_time_limit(1000000);
        ini_set("memory_limit", "256M");

        $this->autoRender = false;

        for($division = 2; $division <= 13; $division++) {
            if($division == 11) continue;

            for($page = 1; $page <= 3; $page++) {
                $site_url = "http://games.crossfit.com/scores/leaderboard.php?stage=1&sort=0&division=".$division."&region=0&numberperpage=5000&page=".$page."&competition=0&frontpage=0&expanded=0&full=1&year=13&showtoggles=0&hidedropdowns=0&showathleteac=1&athletename=";

                $html = $this->getResponse($site_url);

                $this->AthleteScore = ClassRegistry::init("AthleteScore");
                $this->Athlete = ClassRegistry::init("Athlete");

                $count = 0;

                $start = strpos($html, "<td class=\"name\">");
                while($start !== false) {
                    $html = substr($html, $start);

                    $start = strpos($html, "href") + 6;
                    $end = strpos($html, "\"", $start);

                    $url = substr($html, $start, $end - $start);

                    $start = strpos($html, "/athlete/", $start) + 9;
                    $end = strpos($html, "\"", $start);

                    $id = substr($html, $start, $end - $start);

                    $start = strpos($html, "_top", $end) + 6;
                    $end = strpos($html, "</a>", $start);
                    
                    $name = substr($html, $start, $end - $start);
                    echo $id. " " .$name . " " . $url. "<br/>";

                    if(!$this->Athlete->findByGid($id)) {
                        $user = $this->parseUserDetails($url);

                        if(!isset($user['scores']['wod'])) continue;

                        $this->Athlete->create();
                        $this->Athlete->save(array("name" => $name, "region" => $user['region'], "region_url" => $user['region_url'], "affiliate" => $user['affiliate'],
                            "affiliate_url" => $user['affiliate_url'], "gender" => $user['gender'], "age" => $user['age'], "height" => $user['height'], "weight" => $user['weight'], "gid" => $id, "image" => $user['image']));

                        $this->AthleteScore->create();
                        $this->AthleteScore->save(array("athlete_id" => $this->Athlete->id, "wod1" => $user['scores']['wod1']['score'],
                            "wod2" => $user['scores']['wod2']['score'], "wod3" => $user['scores']['wod3']['score'], "wod4" => $user['scores']['wod4']['score'],
                            "wod5" => $user['scores']['wod5']['score']));
                    }
                    
                    $start = strpos($html, "<td class=\"name\">", $start);

                    $count++;
                }
            }
        }
    }

    private function parseUserDetails($url) {
        $html = $this->getResponse($url);

        $start = strpos($html, "id=\"page-title\">Athlete: ") + 23;
        if($start !== false) {
            $end = strpos($html, "</h2>", $start);

            $name = trim(substr($html, $start, $end - $start));

            if($name != "Not found") {
                $start = strpos($html, "profile-image");
                $start = strpos($html, "<img src", $start) + 10;
                $end = strpos($html, "\"", $start);

                $image = substr($html, $start, $end - $start);

                if(strpos($image, "pukie.png") !== false) $image = "http://games.crossfit.com" . $image;

                $start = strpos($html, "set_region_title_class", $end);
                $start = strpos($html, "href", $start) + 6;
                $end = strpos($html, "\"", $start);

                $region_url = "http://games.crossfit.com" . substr($html, $start, $end - $start);

                $start = $end + 2;
                $end = strpos($html, "</a>", $start);
                $region = substr($html, $start, $end - $start);

                $start = strpos($html, "Affiliate:", $end);
                $start = strpos($html, "<dd>", $start);
                $start = strpos($html, "href", $start) + 6;
                $end = strpos($html, "\"", $start);

                $affiliate_url = "http://games.crossfit.com" . substr($html, $start, $end - $start);

                $start = $end + 2;
                $end = strpos($html, "</a>", $start);
                $affiliate = substr($html, $start, $end - $start);

                $start = strpos($html, "<dd>", $end) + 4;
                $end = strpos($html, "</dd>", $start);
                $gender = substr($html, $start, $end - $start);

                $start = strpos($html, "<dd>", $end) + 4;
                $end = strpos($html, "</dd>", $start);
                $age = substr($html, $start, $end - $start);

                $start = strpos($html, "<dd>", $end) + 4;
                $end = strpos($html, "</dd>", $start);
                $height = substr($html, $start, $end - $start);

                $start = strpos($html, "<dd>", $end) + 4;
                $end = strpos($html, "</dd>", $start);
                $weight = substr($html, $start, $end - $start);

                $result = array("name" => $name, "image" => $image, "region" => $region, "region_url" => $region_url, 
                    "affiliate" => $affiliate, "affiliate_url" => $affiliate_url, "gender" => $gender,
                    "age" => $age, "height" => $height, "weight" => $weight);

                $start = strpos($html, "cf_leaderboard", $end) + 21;
                $end = strpos($html, "\"", $start);

                $url = substr($html, $start, $end - $start);

                $result['scores'] = $this->parseScores($url);

                return $result;
            }
        }
        return false;
    }

    private function parseScores($url) {
        $html = $this->getResponse($url);

        if(strpos($html, "No scores yet") > 0) {
            return array();
        }

        $result = array();

        $end = strpos($html, "<tr class=\"highlight\">");

        return $this->getScores($html, $end);
    }

    private function getScores($html, $end) {
    	$result = array();

    	for($i = 0; $i < 5; $i++) {
            $start = strpos($html, "score-cell", $end);
            $start = strpos($html, "<span", $start);
            $start = strpos($html, ">", $start) + 1;
            $end = strpos($html, "\"", $start);

            $score = substr($html, $start, $end - $start);

            $key = "wod".($i+1);
            $start = strpos($score, "(");
            if($start !== false) {
                $rank = substr($score, 0, $start);
                $score = substr($score, $start+1, strpos($score, ")") - $start - 1);

                $result[$key]['rank'] = trim($rank) == "--" ? "" : trim($rank);
                $result[$key]['score'] = trim($score) == "--" ? "" : trim($score);
            }
            else {
                $result[$key]['rank'] = "";
                $result[$key]['score'] = "";
            }
        }

        return $result;
    }

    private function getResponse($url, $fields = false, $ssl = false) {
        $useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
        
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
//        $response = curl_exec($ch);
//        curl_close($ch);
//        return $response;
        
//        $ckfile = tempnam ("/tmp", "CURLCOOKIE");
        
        $fields_string = "";
        if ($fields !== false) {
            foreach($fields as $key => $value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
        }
        
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        if ($fields !== false) {
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        } 
        if ($ssl) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        //execute post
        $result = curl_exec($ch);
        
        if ($error = curl_error($ch)) { 
            echo "Error: $error<br />\n"; 
        } 

        //close connection
        curl_close($ch);
        
        return $result;
   }
}
