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

class UsersController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Users';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	var $components = array("Cookie", "RequestHandler");

	var $helpers = array("Html", "Form");

	public function getDetails() {
		$this->autoRender = false;

		if($this->Session->check("id")) {
			$this->User = ClassRegistry::init("User");

			$user = $this->User->findById($this->Session->read("id"), array("email", "name"));

			if($user) {
				$retval = array("result" => "SUCCESS", "email" => $user['User']['email'], "name" => $user['User']['name']);
			}
			else {
				$retval = array("result" => "FAILURE", "message" => "Unable to locate account information.");
			}
		}
		else {
			$retval = array("result" => "FAILURE", "message" => "Your session has expired.");
		}

		echo json_encode($retval);
	}

	public function save() {
		$this->autoRender = false;

		if($this->Session->check("id")) {
			if($this->data) {
				if(isset($this->data['User']['email']) && isset($this->data['User']['name'])) {
					$email = $this->data['User']['email'];
					$name = $this->data['User']['name'];

					if($email && $name) {
						$this->User = ClassRegistry::init("User");
						$user = $this->User->find("first", array("conditions" => array("email" => $email, "id <>" => $this->Session->read("id"))));

						if(!$user) {
							if($this->User->save(array("id" => $this->Session->read("id"), "name" => $name, "email" => $email))) {
								$this->Session->write("name", $name);
								$this->Session->write("email", $email);

								$retval = array("result" => "SUCCESS");
							}
							else {
								$retval = array("result" => "FAILURE", "message" => "Sorry, unable to update account.");
							}
						}
						else {
							$retval = array("result" => "FAILURE", "message" => "Email already in use.");
						}
					}
					else {
						$retval = array("result" => "FAILURE", "message" => "All fields are required .");
					}
				}
				else {
					$retval = array("result" => "FAILURE", "message" => "All fields are required.");
				}
			}
			else {
				$retval = array("result" => "FAILURE", "message" => "All fields are required.");
			}

		}
		else {
			$retval = array("result" => "FAILURE", "message" => "Your session has expired.");
		}

		echo json_encode($retval);
	}

	public function claimAccount() {
		$this->autoRender = false;

		if($this->Session->check("id")) {
			if($this->data) {
				if(isset($this->data['message'])) {
					$this->User = ClassRegistry::init("User");
					$user = $this->User->findById($this->Session->read("id"));

					$Email = new CakeEmail('default');
					$Email->template('claim_account');
					$Email->viewVars(array("message" => $this->data['message'], "name" => $user['User']['name'], "email" => $user['User']['email']));
					$Email->from(array('do-no-respond@crossfitalerts.com' => 'Crossfit Alerts'));
					$Email->to("crossfitalerts@gmail.com");
					$Email->subject('Crossfit Alerts - Claim Account');
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
		}
		else {
			$retval = array("result" => "FAILURE", "message" => "Your session has expired.");
		}

		echo json_encode($retval);
	}

	public function myAccount() {
		$this->set("title_for_layout", "My Account");

		if($this->Session->check("id")) {
			$user_id = $this->Session->read("id");

			$joins = array(array("table" => "athletes", "alias" => "Athlete", "type" => "LEFT", "conditions" => array("Athlete.id = User.athlete_id")));

			$this->User = ClassRegistry::init("User");
			$user = $this->User->find("first", array("conditions" => array("User.id" => $user_id), "joins" => $joins, "fields" => array("User.name",
				"User.email", "Athlete.*", "(select count(*) from users_athletes ua where ua.user_id = User.id) as follows",
				"(select count(*) from users_athletes ua, users u where ua.athlete_id = User.athlete_id and u.id = ua.user_id) as following_me")));

			$this->set("user", $user);
		}
		else {
			$this->Session->setFlash("Sorry, but your session has expired.");
			$this->redirect("/");
		}
	}

	public function logout() {
		$this->Session->destroy();
        $this->Cookie->destroy();

        $this->redirect("/pages/home");
	}

	public function followers() {
		if($this->Session->check("id")) {
			$this->set("title_for_layout", "People that follow me");

			$user_id = $this->Session->read("id");
			$athlete_id = $this->Session->read("athlete_id");

			$joins = array(array("table" => "users", "alias" => "User", "type" => "INNER", "conditions" => array("User.id = UsersAthlete.user_id"))
				);

			$this->UsersAthlete = ClassRegistry::init("UsersAthlete");

			$users = $this->UsersAthlete->find("all", array("joins" => $joins, "conditions" => array("UsersAthlete.athlete_id" => $athlete_id), "fields" => array("User.id", "User.name", "User.athlete_id", 
				"(select count(*) from users_athletes ua where ua.user_id = ".$user_id." and ua.athlete_id = User.athlete_id) as is_following")));
			
			$this->set("users", $users);
		}
		else {
			$this->Session->setFlash("Sorry, but your session has expired.");
			$this->redirect("/");
		}
	}

	public function follows() {
		if($this->Session->check("id")) {
			$this->set("title_for_layout", "Athletes I Follow");

			$user_id = $this->Session->read("id");

			$joins = array(array("table" => "athlete_scores", "alias" => "AthleteScore", "type" => "INNER", "conditions" => array("AthleteScore.athlete_id = Athlete.id")),
				array("table" => "users_athletes", "alias" => "UserAthlete", "type" => "INNER", "conditions" => array("UserAthlete.athlete_id = Athlete.id", "UserAthlete.user_id" => $user_id)));

			$this->Athlete = ClassRegistry::init("Athlete");

			$athletes = $this->Athlete->find("all", array("joins" => $joins, "limit" => 1000, "fields" => array("Athlete.*", "AthleteScore.*", 
				"(select count(*) from users_athletes ua where ua.athlete_id = Athlete.id and ua.user_id = ".$user_id.") as follow")));
			$this->set("athletes", $athletes);
			$this->set("first_load", 0);
		}
		else {
			$this->Session->setFlash("Sorry, but your session has expired.");
			$this->redirect("/");
		}
	}

	public function login() {
		$this->autoRender = false;

		if($this->data) {
			if(isset($this->data['User']['email']) && isset($this->data['User']['password'])) {
				$email = $this->data['User']['email'];
				$password = $this->data['User']['password'];

				if($email && $password) {
					$user = $this->User->find("first", array("fields" => array("User.id", "User.email", "User.name", "User.athlete_id"), "conditions" => array("email" => $email, "password" => md5($password))));
					if($user) {
						$this->Session->write("athlete_id", $user['User']['athlete_id']);
						$this->Session->write("name", $user['User']['name']);
						$this->Session->write("email", $email);
						$this->Session->write("id", $user['User']['id']);

						$retval = array("result" => "SUCCESS", "return_url" => "/?c=1");
					}
					else {
						$retval = array("result" => "FAILURE", "message" => "Invalid login credentials.");
					}
				}
				else {
					$retval = array("result" => "FAILURE", "message" => "All fields are required to create an account.");
				}
			}
			else {
				$retval = array("result" => "FAILURE", "message" => "All fields are required to create an account.");
			}
		}
		else {
			$retval = array("result" => "FAILURE", "message" => "All fields are required to create an account.");
		}

		echo json_encode($retval);
	}

	public function saveJoinInfo() {
		$this->autoRender = false;

		if($this->data) {
			if(isset($this->data['User']['email']) && isset($this->data['User']['password']) && isset($this->data['User']['name'])) {
				$email = $this->data['User']['email'];
				$password = $this->data['User']['password'];
				$name = $this->data['User']['name'];

				if($email && $password && $name) {
					if(!$this->User->findByEmail($email)) {
						$this->User->create();
						$this->User->save(array("email" => $email, "password" => md5($password), "name" => $name, "created_date" => date("Y-m-d H:i:s")));

						$Email = new CakeEmail('default');
						$Email->template('welcome_email');
						$Email->viewVars(array("name" => $name));
						$Email->from(array('do-no-respond@crossfitalerts.com' => 'Crossfit Alerts'));
						$Email->to($email);
						$Email->subject('Welcome to Crossfit Alerts');
						$Email->emailFormat('both');
						$Email->send();

						$this->Session->write("name", $name);
						$this->Session->write("email", $email);
						$this->Session->write("id", $this->User->id);

						$retval = array("result" => "SUCCESS", "return_url" => "/?first=1");
					}
					else {
						$retval = array("result" => "FAILURE", "message" => "Email already exists for a user.");
					}
				}
				else {
					$retval = array("result" => "FAILURE", "message" => "All fields are required.");
				}
			}
			else {
				$retval = array("result" => "FAILURE", "message" => "All fields are required.");
			}
		}
		else {
			$retval = array("result" => "FAILURE", "message" => "All fields are required.");
		}

		echo json_encode($retval);
	}
}
