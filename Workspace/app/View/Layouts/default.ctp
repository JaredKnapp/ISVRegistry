<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10" />
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo (isset($title)?$title:$this->fetch('title')); ?>
    </title>
    <?php
	echo $this->Html->meta('icon');

	echo $this->Html->css('default');
	echo $this->Html->css('theme');
    ?>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
    <!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

    <?php
	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
    ?>
</head>
<body>
    <div id="container">
        <div id="wrapper">
            <div id="header">
                <h1>
                    <?php echo $this->Html->link($this->Session->read('org.name').' ISV Registry', array('controller' => 'Isv', 'action' => 'index')); ?>
                </h1>
                <div id="accountinfo" class="nav topcorner">
                    <ul>
                        <?php
                        if(is_array($orglist) && count($orglist) > 1) {
                        ?>
                            <li>
                                <select id="org_selector" style="padding:0px 0px 0px 0px" >
                                <?php
                                $org = $this->Session->read('org.id');
                                foreach($orglist as $index=>$item){
                                    echo "<option value=$index " . ($index==$org ? 'SELECTED' : '') . ">$item</option>";
                                }
                                echo '<option value="0"'. ('0'==$org ? 'SELECTED' : '') . '>All</option>';
                                ?>
                                </select>
                            </li>
                        <?php
                        }
                        ?>
                        <?php
						if (AuthComponent::user('id')) {
							echo '<li>'.$this->Html->link(AuthComponent::user('email'),array('controller' => 'Account', 'action' => 'myAccount')).'</li>';
						} else {
							echo '<li>'.$this->Html->link('Log In',array('controller' => 'auth', 'action' => 'login')).'</li>';
						} ?>
                    </ul>
                </div>
                <nav id="primary_nav_wrap">
                    <ul>
                        <li>
                            <?php echo $this->Html->link('Validations', array('controller' => 'Isv', 'action' => 'index'), array('class'=>(($this->params['controller'] == 'Isv')? 'active' : 'notactive'))); ?>
                            <ul>
                                <li>
                                    <?php echo $this->Html->link('ISV Registry', array('controller' => 'Isv', 'action' => 'validationRegistry')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Validation Schedule', array('controller' => 'Isv', 'action' => 'validationSchedule')); ?>
                                </li>
                            </ul>
                        </li>
                        <?php
						if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
							echo '<li>'.$this->Html->link('Partners', array('controller' => 'Partners', 'action' => 'index'), array('class'=>(($this->params['controller'] == 'Partners')? 'active' : 'notactive'))).'</li>';
						}
                        ?>
                        <?php
						if (AuthComponent::user('id') && AuthComponent::user('role')==='admin') {
                        ?>
                        <li>
                            <?php echo $this->Html->link('Settings', array('controller' => 'Users', 'action' => 'index'), array('class'=>(in_array($this->params['controller'], array('Users', 'Settings'))? 'active' : 'notactive'))); ?>
                            <ul>
                                <li>
                                    <?php echo $this->Html->link('User Accounts', array('controller' => 'Users', 'action' => 'index')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Workloads and Industries', array('controller' => 'Settings', 'action' => 'industriesIndex')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('EMC Platforms', array('controller' => 'Settings', 'action' => 'platformsIndex')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Organizations', array('controller' => 'Settings', 'action' => 'organizationsIndex')); ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('About', array('controller' => 'Settings', 'action' => 'about')); ?>
                                </li>
                            </ul>
                        </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>

            <div id="content">
                <div id="breadcrumbs">
                    <?php echo $this->Html->getCrumbs(' > ', 'Home'); ?>
                </div>
                <h1>
                    <?php echo (isset($title)?$title:$this->fetch('title')); ?>
                </h1>
                <?php echo $this->Flash->render(); ?>
                <?php echo $this->fetch('content'); ?>
            </div>
            <div id="sqldump">
                <?php echo $this->element('sql_dump'); ?>
            </div>
            <div id="push"></div>
        </div>
        <div id="footer">
            <div id="copyright">
                <?php
				$roadmapLink = $this->Html->link(Configure::read('version'), array('controller'=>'Settings', 'action'=>'roadmap'));
                ?>
                <p>
                    &copy; <?php echo date("Y"); ?> - EMC Corporation. All rights reserved. (Validation Engine: v<?php echo $roadmapLink; ?>)
                </p>
            </div>
            <div id="contactlink">
                <a href="mailto:ETD.Solutions.Architecture@emc.com?subject=Online ISV Registry">Contact Us</a>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            $("#org_selector").change(function () {
<?php
$linkParams = array(
    'controller'=>$this->request->params['controller'],
    'action'=>$this->request->params['action']
    );

$linkParams = array_merge($linkParams, $this->request->params['named']);

if(array_key_exists('show-platforms', $linkParams)){
    unset($linkParams['show-platforms']);
}

if(array_key_exists('page', $linkParams)){
    $linkParams['page'] = 1;
}

if(array_key_exists('pass', $this->request->params)){
    $linkParams = array_merge($linkParams, $this->request->params['pass']);
}

$url = $this->Html->url($linkParams, true);
echo 'window.location="'.$url.'?org=" + this.value;';
?>
          });
        });
    </script>
</body>

</html>
