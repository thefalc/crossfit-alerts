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

class EmailQueuesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'EmailQueues';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	var $components = array("Cookie", "RequestHandler");

	var $helpers = array("Html", "Form");

    public function announcementEmail() {
        $this->autoRender = false;

        $default = ini_get('max_execution_time');
        set_time_limit(1000000);
        ini_set("memory_limit", "256M");

        $this->User = ClassRegistry::init("User");
        $users = $this->User->find("all", array("fields" => array("User.name", "User.email")));

        foreach($users as $user) {
            $Email = new CakeEmail('default');
            $Email->template('announcement_email');
            $Email->viewVars(array("name" => $user['User']['name']));
            $Email->from(array('do-no-respond@crossfitalerts.com' => 'Crossfit Alerts'));
            $Email->to($user['User']['email']);
            $Email->subject("Chrome extension available for Crossfit Alerts");
            $Email->emailFormat('both');
            $Email->send();
        }
    }

    public function processFollowerQueue() {
        $this->autoRender = false;

        $default = ini_get('max_execution_time');
        set_time_limit(1000000);
        ini_set("memory_limit", "256M");

        $this->FollowerQueue = ClassRegistry::init("FollowerQueue");

        $joins = array(array("table" => "users", "alias" => "User", "type" => "INNER", "conditions" => array("FollowerQueue.user_id = User.id")),
            array("table" => "users", "alias" => "Receiver", "type" => "INNER", "conditions" => array("FollowerQueue.receiver_id = Receiver.id")));

        $queue = $this->FollowerQueue->find("all", array("joins" => $joins, "limit" => 20, "fields" => array("FollowerQueue.id", "User.name", "User.athlete_id", "Receiver.name", "Receiver.email"), "conditions" => array("FollowerQueue.sent" => 0)));

        foreach($queue as $record) {
            $Email = new CakeEmail('default');
            $Email->template('follower_added');
            $Email->viewVars(array("name" => $record['User']['name'], "athlete_id" => $record['User']['athlete_id'],  "receiver_name" => $record['Receiver']['name']));
            $Email->from(array('do-no-respond@crossfitalerts.com' => 'Crossfit Alerts'));
            $Email->to($record['Receiver']['email']);
            $Email->subject($record['User']['name'] . " Started Following You");
            $Email->emailFormat('both');
            $Email->send();

            $record['FollowerQueue']['sent'] = 1;
            $this->FollowerQueue->save($record);
        }
    }

	public function processQueue() {
        $this->autoRender = false;

        $default = ini_get('max_execution_time');
        set_time_limit(1000000);
        ini_set("memory_limit", "256M");

        $this->EmailQueue = ClassRegistry::init("EmailQueue");

        $joins = array(array("table" => "users_athletes", "alias" => "UsersAthlete", "type" => "inner", "conditions" => array("UsersAthlete.athlete_id = EmailQueue.athlete_id")),
            array("table" => "users", "alias" => "User", "type" => "inner", "conditions" => array("User.id = UsersAthlete.user_id")),
            array("table" => "athletes", "alias" => "Athlete", "type" => "inner", "conditions" => array("Athlete.id = EmailQueue.athlete_id")));

        $emails = $this->EmailQueue->find("all", array("joins" => $joins, "conditions" => array("sent" => 0), "limit" => 20,
            "fields" => array("User.email", "Athlete.name", "Athlete.id", "Athlete.gender", "Athlete.gid", "EmailQueue.id", "EmailQueue.workout_number", "EmailQueue.rank", "EmailQueue.is_update", "EmailQueue.score")));
        
        foreach($emails as $athlete) {
            $email = new CakeEmail('default');
            $email->viewVars(array("athlete" => $athlete));
            $email->template('alert_email');
            $email->from(array('do-no-respond@crossfitalerts.com' => 'Crossfit Alerts'));
            $email->to($athlete['User']['email']);
            $email->subject("Score update from Crossfit Alerts");
            $email->emailFormat('both');
            $email->send();

            $athlete['EmailQueue']['sent'] = 1;
            $this->EmailQueue->save($athlete);
        }
    }
}
