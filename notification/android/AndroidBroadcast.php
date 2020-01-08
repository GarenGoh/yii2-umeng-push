<?php
namespace garengoh\umeng\notification\android;
use garengoh\umeng\notification\AndroidNotification;

class AndroidBroadcast extends AndroidNotification {
	function  __construct() {
		parent::__construct();
		$this->data["type"] = "broadcast";
	}
}