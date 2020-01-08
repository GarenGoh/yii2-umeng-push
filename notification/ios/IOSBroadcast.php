<?php
namespace garengoh\umeng\notification\ios;
use garengoh\umeng\notification\IOSNotification;

class IOSBroadcast extends IOSNotification {
	function  __construct() {
		parent::__construct();
		$this->data["type"] = "broadcast";
	}
}