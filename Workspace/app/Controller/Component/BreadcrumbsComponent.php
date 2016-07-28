<?php
App::uses('Component', 'Controller');
App::uses('Component', 'Session');

class BreadcrumbsComponent extends Component {
	
	public $components = array('Session');
	
	public function pushCrumb($label, $link = null, $reset = false){
		
		$base = Router::url('/');
		if(!is_null($link) && $base === substr($link, 0, strlen($base))){
			$link = substr($link, strlen($base) - 1);
		}
		
		if ($reset) {
			$this->Session->write('breadcrumbs', array());
		}
		
		if (!empty($label)) {
			$breadcrumbs = $this->Session->read('breadcrumbs');
			if(!$breadcrumbs) $breadcrumbs = array();
			$breadcrumbs[] = compact('label', 'link');
			$this->Session->write('breadcrumbs', $breadcrumbs);
		}
		return $this;	
	}
	
}