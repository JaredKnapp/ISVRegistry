<?php
App::uses('Component', 'Controller');

class FilterComponent extends Component {

	public function filter($controller){
		$conditions = array();

		//Transform POST into GET
		if(($controller->request->is('post') || $controller->request->is('put')) && isset($controller->data['Filter'])){
			
			$filter_url['controller'] = $controller->request->params['controller'];
			$filter_url['action'] = $controller->request->params['action'];

			// We need to overwrite the page every time we change the parameters
			$filter_url['page'] = 1;

			// for each filter we will add a GET parameter for the generated url
			foreach($controller->data['Filter'] as $name => $value){
				if($value){
					if(is_array($value)){
						$filter_url[$name] = $value;
					} else {
						$filter_url[$name] = urlencode($value);
					}
				}
			}
			
			if(isset($controller->data['Show'])){
				foreach($controller->data['Show'] as $name => $value){
					$filter_url['show_'.$name] = $value;
				}
			}
			
			// now that we have generated an url with GET parameters, we'll redirect to that page
			return $controller->redirect($filter_url);
		} else {
			// Inspect all the named parameters to apply the filters
			foreach($controller->params['named'] as $param_name => $value){
				// Don't apply the default named parameters used for pagination
				if(substr($param_name, 0, min(5, strlen($param_name)))==='show_'){
					$controller->request->data['Show'][substr($param_name, 5)] = $value;
					$conditions['show'][$param_name] = $value;
				} else if(!in_array($param_name, array('page','sort','direction','limit','collapsed'))){
					if(is_array($value)){
						$conditions[str_replace('-', '.', $param_name)] = $value;
					} else {
						if($param_name=='id' || substr($param_name, -3)=='-id' || substr($param_name, -3)=='_id'){
							$conditions[str_replace('-', '.', $param_name).'='] = $value;
						} else {
							$conditions[str_replace('-', '.', $param_name)] = $value;
						}
					}
					$controller->request->data['Filter'][$param_name] = $value;
				} else if(in_array($param_name, array('limit','collapsed'))){
					$controller->request->data['Filter'][$param_name] = $value;
				}
			}
		}
		
		return $conditions;
	}
}
