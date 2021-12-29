<?php

App::uses('Shell', 'Console');

class EmailShell extends Shell {
	
	public function main() {
		$this->requestAction("email_queues/processQueue");
	}
}
