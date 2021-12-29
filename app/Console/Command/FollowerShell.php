<?php

App::uses('Shell', 'Console');

class FollowerShell extends Shell {
	
	public function main() {
		$this->requestAction("email_queues/processFollowerQueue");
	}
}
