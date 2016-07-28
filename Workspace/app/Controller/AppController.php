<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $uses = array('Organization');
	public $components = array(
        'Session',
        'Flash',
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'Isv',
				'action' => 'validationRegistry'
            ),
            'logoutRedirect' => array(
                'controller' => 'Isv',
				'action' => 'validationRegistry',
                'home'
            ),
            'authenticate' => array(
                'Form' => array(
					'fields' => array('username' => 'email', 'password' => 'password'),
					'userModel' => 'User',
                    'passwordHasher' => 'Blowfish'
                )
            )
        )
    );

    public function beforeFilter() {
		parent::beforeFilter();

        if(array_key_exists('org', $this->request->query)){
            $orgString = $this->request->query['org'];

            if($orgString==='0') {
                $this->Session->write('org.id', '0');
                $this->Session->write('org.name', 'Combined');
            } elseif(is_numeric($orgString)) {
                $orgRecord = $this->Organization->findById($orgString);
                if($orgRecord){
                    $this->Session->write('org.id', $orgRecord['Organization']['id']);
                    $this->Session->write('org.name', $orgRecord['Organization']['name']);
                }
            } else {
                $orgRecord = $this->Organization->find('first', array('conditions'=>array('name LIKE'=>$orgString.'%')));
                if($orgRecord){
                    $this->Session->write('org.id', $orgRecord['Organization']['id']);
                    $this->Session->write('org.name', $orgRecord['Organization']['name']);
                }
            }
        } 
        
        if(!$this->Session->check('org.id')){
            $defaultorg = $this->Organization->find('first', array('conditions'=>array('is_default'=>true)));
            if($defaultorg){
                $this->Session->write('org.id', $defaultorg['Organization']['id']);
                $this->Session->write('org.name', $defaultorg['Organization']['name']);
            }
        }

        $this->set('orglist', $this->Organization->find('list'));
    }
}
