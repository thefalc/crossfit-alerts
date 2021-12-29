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
App::uses('CakeEmail', 'Network/Email');

class AthletesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Athletes';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	var $components = array("Cookie", "RequestHandler");

	var $helpers = array("Html", "Form");

	public function search($query = "") {
		$this->layout = "ajax";

		$start = 0;
		if(isset($this->params['url']['n'])) $start = $this->params['url']['n'];

		$joins = array(array("table" => "athlete_scores", "alias" => "AthleteScore", "type" => "INNER", "conditions" => array("AthleteScore.athlete_id = Athlete.id")));

		if($this->Session->check("id")) {
			$user_id = $this->Session->read("id");
		} 
		else {
			$user_id = 0;
		}

		$this->Athlete = ClassRegistry::init("Athlete");

		$athletes = $this->Athlete->find("all", array("joins" => $joins, "limit" => 20, "offset" => $start, "fields" => array("Athlete.*", "AthleteScore.*", 
			"(select count(*) from users_athletes ua where ua.athlete_id = Athlete.id and ua.user_id = ".$user_id.") as follow"), "order" => array("AthleteScore.wod2 + AthleteScore.wod1 DESC"), 
			"conditions" => array("OR" => array("name like" => $query."%", "affiliate like" => $query."%", "region like" => $query."%"))));
		$this->set("athletes", $athletes);

		if($start == 0) {
			$this->render("/Elements/athletes");	
		}
		else {
			$this->render("/Elements/athletes_more");
		}
	}

	function toggleFollow($athlete_id) {
		$this->autoRender = false;

		if($this->Session->check("id")) {
			$user_id = $this->Session->read("id");

			$this->UsersAthlete = ClassRegistry::init("UsersAthlete");
			$record = $this->UsersAthlete->find("first", array("conditions" => array("user_id" => $user_id, "athlete_id" => $athlete_id)));
			// follow athlete
			if(!$record) {
				$this->UsersAthlete->create();
				$this->UsersAthlete->save(array("user_id" => $user_id, "athlete_id" => $athlete_id, "created_date" => date("Y-m-d H:i:s")));

				$this->User = ClassRegistry::init("User");
				$user = $this->User->find("first", array("conditions" => array("athlete_id" => $athlete_id)));

				if($user) {
					$this->FollowerQueue = ClassRegistry::init("FollowerQueue");
					$this->FollowerQueue->create();
					$this->FollowerQueue->save(array("user_id" => $this->Session->read("id"), "receiver_id" => $user['User']['id'], "sent" => 0, "created_date" => date("Y-m-d H:i:s")));
				}

				$retval = array("result" => "SUCCESS", "follow" => true);
			}
			else { // unfollow athlete
				if($this->UsersAthlete->delete($record['UsersAthlete']['id'])) {
					$retval = array("result" => "SUCCESS", "follow" => false);
				}
			}
		}
		else {
			$retval = array("result" => "FAILURE", "message" => "You must be logged in.");
		}
		
		echo json_encode($retval);
	}
}
