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

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	var $helpers = array("Html", "Form");

	public function tools() {
		$this->set("title_for_layout", "Crossfit Alerts | Tools for you");
	}

	public function contactSubmit() {
		$this->autoRender = false;

		if($this->data) {
			if(isset($this->data['message'])) {
				$email = $this->Session->check("email") ? $this->Session->read("email") : $this->data['email'];

				$Email = new CakeEmail('default');
				$Email->template('contact_request');
				$Email->viewVars(array("message" => $this->data['message'], "email" => $email));
				$Email->from(array('do-no-respond@crossfitalerts.com' => 'Crossfit Alerts'));
				$Email->to("crossfitalerts@gmail.com");
				$Email->subject('Crossfit Alerts - Contact Request');
				$Email->emailFormat('both');
				$Email->send();

				$retval = array("result" => "SUCCESS");
			}
			else {
				$retval = array("result" => "FAILURE", "message" => "You must provide a message.");
			}
		}
		else {
			$retval = array("result" => "FAILURE", "message" => "You must provide a message.");
		}

		echo json_encode($retval);
	}

	public function contact() {
		$this->set("title_for_layout", "Crossfit Alerts | Contact me");
	}

	public function home() {
		$this->set("title_for_layout", "Crossfit Alerts | Track and follow your favorite athletes");

		if($this->Session->check("id")) {
			$user_id = $this->Session->read("id");
		} 
		else {
			$user_id = 0;
		}

		$query = "";
		if(isset($this->params['url']['q'])) $query = $this->params['url']['q'];

		$first = 0;
		if(isset($this->params['url']['first'])) $first = $this->params['url']['first'];

		$claim = 0;
		if(isset($this->params['url']['c'])) $claim = $this->params['url']['c'];

		$id = "";
		if(isset($this->params['url']['id'])) $id = $this->params['url']['id'];

		$this->set("claim", $claim);

		if($first) $this->set("first_load", true);
		else $this->set("first_load", false);

		$this->set("query", $query);

		$this->Athlete = ClassRegistry::init("Athlete");
		$joins = array(array("table" => "athlete_scores", "alias" => "AthleteScore", "type" => "INNER", "conditions" => array("AthleteScore.athlete_id = Athlete.id")));

		if($id) {
			$athletes = $this->Athlete->find("all", array("joins" => $joins, "limit" => 20, "fields" => array("Athlete.*", "AthleteScore.*", 
			"(select count(*) from users_athletes ua where ua.athlete_id = Athlete.id and ua.user_id = ".$user_id.") as follow"), "order" => array("AthleteScore.wod2 + AthleteScore.wod1 DESC"), "conditions" => array("Athlete.id" => $id)));
		}
		else {
			$athletes = $this->Athlete->find("all", array("joins" => $joins, "limit" => 20, "fields" => array("Athlete.*", "AthleteScore.*", 
			"(select count(*) from users_athletes ua where ua.athlete_id = Athlete.id and ua.user_id = ".$user_id.") as follow"), "order" => array("AthleteScore.wod2 + AthleteScore.wod1 DESC"), "conditions" => array("OR" => array("name like" => $query."%", "affiliate like" => $query."%", "region like" => $query."%"))));
		}
		
		$this->set("athletes", $athletes);
	}
}
