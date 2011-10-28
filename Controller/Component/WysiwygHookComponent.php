<?php
class WysiwygHookComponent extends Component {
	var $Controller = null;
	var $components = array('Hook');

	public function initialize(&$Controller){
		$this->Controller = $Controller;
	}
}