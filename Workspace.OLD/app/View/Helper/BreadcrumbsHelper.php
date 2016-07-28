<?php
/**
 * Breadcrumbs helper
 * Allows to generate and display breadcrumbs with a convenient syntax
 *
 * It uses a <ul><li> syntax but can be extended and protected method overriden to
 * generate the markup adapted to your situation
 * 
 */

class BreadcrumbsHelper extends AppHelper {
	/**
	 * Helpers needed
	 *
	 * @var array
	 */
	
	public $helpers = array('Html', 'Session');
	
	/**
	 * Separator string to use between each crumb
	 * Set an empty string to not use a text separator
	 *
	 * @var string
	 */
	public $separator = ' &gt; ';
	
	public function __construct( $view , $settings ){
		parent::__construct($view , $settings );
	}

	/**
	* Gets the breadcrumbs list
	*
	* @param mixed $home True to include a link to the homepage, or false, or the
	*	name of the link to the homepage
	* @param boolean $reset If true the breadcrumbs list will also be cleared
	* @return string Markup of the list
	*/
	public function getCrumbs($here = null, $home = true) {
		$markup = '';
		$breadcrumbs = $this->Session->read('breadcrumbs');
		if(!$breadcrumbs) $breadcrumbs = array();
		if ($home) {
			if ($home === true) {
				$home = __('Home', true);
			}
			array_unshift($breadcrumbs, array('label' => $home, 'link' => '/index.php/'));
		}
		if (!empty($breadcrumbs)) {
			foreach ($breadcrumbs as $breadcrumb) {
				$markup .= $this->_crumbMarkup($here, $breadcrumb);
			}
			$markup = $this->_wrapCrumbs($markup);
		}
		return $markup;
	}

	/**
	 * Generates the markup for a crumb element
	 * 
	 * @param array $breadcrumb Breadcrumb information, containing a label and a link (optional)
	 * @return string Markup for this single breadcrumb
	 */
	protected function _crumbMarkup($here, $breadcrumb) {
		$label = $breadcrumb['label'];
		$link = $breadcrumb['link'];
		
		
		$base = Router::url('/');
		if(!is_null($link)){
			$base = Router::url('/');
			if(strlen($base) >= strlen($link) && $base === substr($link, 0, strlen($base))){
				$link = substr($link, strlen($base) - 1);
			}
			if ($here == Router::url($link)) {
				$link = null;
			}
		}
		
		return $this->Html->tag(
			'li',
			empty($link)
			? $this->Html->tag('strong', $label) . $this->separator
			: $this->Html->link($label, $link) . $this->separator
			);
	}

	/**
	 * Wraps the markup for crumbs in an element
	 *
	 * @param string $markup
	 * @return string
	 */
	protected function _wrapCrumbs($markup) {
		if (!empty($this->separator)) {
			$posSeparatorToRemove = strrpos($markup, $this->separator);
			$markup = substr($markup, 0, $posSeparatorToRemove) . substr($markup, $posSeparatorToRemove + strlen($this->separator));
		}
		return $this->Html->tag('ul', $markup, array('class' => 'breadcrumbs'));
	}

}