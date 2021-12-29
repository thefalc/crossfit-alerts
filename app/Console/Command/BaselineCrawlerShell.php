<?php

App::uses('Shell', 'Console');

class BaselineCrawlerShell extends Shell {
	
	public function main() {
		$this->requestAction("crawlers/baselineCrawl");
	}
}
