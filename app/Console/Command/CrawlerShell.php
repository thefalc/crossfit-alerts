<?php

App::uses('Shell', 'Console');

class CrawlerShell extends Shell {
	
	public function main() {
		$this->requestAction("crawlers/recrawlAthletes");
	}
}
