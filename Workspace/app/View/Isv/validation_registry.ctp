<?php
$isCollapsed = true;
if(isset($this->request['named']) && array_key_exists('collapsed', $this->request['named'])){
	$isCollapsed = ( $this->request['named']['collapsed'] == 'true' );
}

$levels= Configure::read('Validation.levels');
$statuses= Configure::read('Validation.statusOptions');
?>

<script>
$(function() {
	$( "#accordion" ).accordion({
		active: <?php echo $isCollapsed ? 'false' : 0 ?>,
		activate: function(event, ui) {
				$('#FilterCollapsed').val(ui.newHeader.text() ? false : true);
			},
		heightStyle: "content",
		collapsible: true,
		create: function(event, ui) { $("#accordion").show(); }
	});
});
</script>

<div id="exportlinks">
    <?php
    $linkParams = array(
        'controller'=>$this->request->params['controller'],
        'action'=>$this->request->params['action'],
        'ext'=>'csv',
        'page'=>1
        );

    $linkParams = array_merge($linkParams, $this->request->params['named']);

    echo $this->Html->image('CSV.png',array(
        'height'=>35,
        'border'=>'none',
        'alt'=>'Export to CSV',
        'url'=>$linkParams
        ));
    ?>
</div>

<?php
echo $this->Form->create("Filter");
echo $this->Form->hidden('collapsed');
echo $this->Form->hidden('org');

$certifiedIcon = $this->Html->image('CertifiedCheckmark.png', array('height'=>'20px', 'width'=>'20px', 'class'=>'certifiedicon', 'title'=>'ISV Certified Validation'));
$certifiedIconLegend = $this->Html->image('CertifiedCheckmark.png', array('height'=>'15px', 'width'=>'15px', 'title'=>'ISV Certified Validation'));
?>

<!--
**************************************
******** VIEW FILTERS
**************************************
-->
<?php echo $this->Form->text("searchtext", array('placeholder' => "Search View...", 'default' => '')); ?>
<div id="filters">
    <div id="accordion">
        <span>
            <span>
                <strong>Additional&nbsp;Filters</strong>
                &nbsp;-&nbsp;Showing&nbsp;Statuses:&nbsp;
            </span>
            <span>
                <?php
                $separator = '';
                foreach($this->request->data['Filter']['versions-status'] as $status){
                    echo $separator.'<div class="statusovalsmall" style="background-color:'.$statuses[$status]['color']['back'].'; color:'.$statuses[$status]['color']['fore'].'; border-color:'.$statuses[$status]['color']['border'].';">&nbsp;</div>&nbsp;'.$statuses[$status]['label'];
                    $separator = ',&nbsp;';
                }
                echo '<br />Checkmark badge '.$certifiedIconLegend.' indicates ISV Certified validations.';
                ?>
            </span>
        </span>
        <div>
            <table width="100%">
                <tr>
                    <td width="33%">
                        <?php
						$options = array();
						foreach($levels as $key=>$value){
							$options[$key] = $value['label'];
						}
						echo $this->Form->input('level', array(
                            'type'=>'select',
                            'escape'=>false,
                            'label'=>array('text'=>'<strong>Validation Levels</strong>', 'escape'=>false),
                            'multiple'=>'checkbox',
                            'options'=>$options));
                        echo $this->Form->input('viewoptions', array(
                            'type'=>'select',
                            'label'=>array('text'=>'<strong>Options</strong>', 'escape'=>false),
                            'multiple'=>'checkbox',
                            'options'=>array('empty'=>'Exclude ISVs with no validations', 'certified'=>'Exclude uncertified validations')
                            ));
                        ?>
                    </td>
                    <td width="33%">
                        <?php
						$options = array();
						foreach($statuses as $key=>$value){
							$options[$key] = '<div class="statusovalsmall" style="background-color:'.$statuses[$key]['color']['back'].'; color:'.$statuses[$key]['color']['fore'].'; border-color:'.$statuses[$key]['color']['border'].';">&nbsp;</div>&nbsp;'.$value['label'];
						}
						echo $this->Form->input('versions-status', array(
                            'type'=>'select',
                            'escape'=>false,
                            'label'=>array('text'=>'<strong>Available Status Options</strong>', 'escape'=>false),
                            'multiple'=>'checkbox',
                            'options'=>$options
                            ));
                        ?>
                    </td>
                    <td width="33%">
                        <?php
                        echo $this->Form->input('versions-protocol', array('placeholder'=>'Protocol...', 'label'=>array('text'=>'<strong>Protocol</strong>', 'escape'=>false)));
						$options = array();
						foreach($platforms as $index=>$platform){
							$options[$platform['platformversions']['id']] = $platform['platforms']['name']. ' ' . $platform['platformversions']['version'];
						}
						echo $this->Form->input('show-platforms', array('type'=>'select', 'escape'=>false, 'label'=>array('text'=>'<strong>Select Which Platforms to Display</strong>', 'escape'=>false), 'multiple'=>'checkbox', 'options'=>$options));
                        ?>
                    </td>
                </tr>
            </table>
            <?php echo $this->Form->submit('Apply', array('div'=>false)); ?>
        </div>
    </div>
</div>

<!--
**************************************
******** VIEW CONTENT
**************************************
-->

<table>
    <thead>
        <tr>
            <th>
                <?php echo $this->Paginator->sort('partner', 'Partners'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('product', 'Products'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('version', 'Version'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('protocol', 'Protocol'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('industry', 'Industry'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('workload', 'Workload'); ?>
            </th>
            <?php
			if(isset($this->data['Filter']['show-platforms'])){
				$platformIds = array_values($this->data['Filter']['show-platforms']);

				foreach($platforms as $index=>$platform){
					$platformindex = $platform['platformversions']['id'];
					if(in_array($platformindex, $platformIds)){
						echo '      <th style="text-align:center;">' . $this->Paginator->sort('T'.$platformindex.'_level', $platform['platforms']['name'] . ' ' . $platform['platformversions']['version']) . '</th>'."\r\n";
					}
				}
			}

            foreach($organizations as $index=>$organization){
                echo '<th># '.$organization.'<br />Validations</th>';
            }

			if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
				echo '<th style="text-align:center;vertical-align:middle;">Actions</th>';
			}
            ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($validations as $validation): ?>
        <tr>
            <td>
                <?php
                  //echo var_dump($validation);
                  if(empty($validation['registry']['partner_url'])){
                      echo h($validation['registry']['partner']);
                  } else {
                      echo $this->Html->link(h($validation['registry']['partner']), $validation['registry']['partner_url'], array('target' => '_blank', 'class'=>'link-underlined-nodecoration'));
                  }
                ?>
            </td>
            <td>
                <?php
                  if(empty($validation['registry']['product_url'])){
                      echo h($validation['registry']['product']);
                  } else {
                      echo $this->Html->link(h($validation['registry']['product']), $validation['registry']['product_url'], array('target' => '_blank', 'class'=>'link-underlined-nodecoration'));
                  }
                ?>
            </td>
            <td>
                <?php echo h($validation['registry']['version']); ?>
            </td>
            <td>
                <?php echo h($validation['registry']['protocol']); ?>
            </td>
            <td>
                <?php
                  if(empty($validation['registry']['industry_url'])){
                      echo h($validation['registry']['industry']);
                  } else {
                      echo $this->Html->link(h($validation['registry']['industry']), $validation['registry']['industry_url'], array('target' => '_blank', 'class'=>'link-underlined-nodecoration'));
                  }
                ?>
            </td>
            <td>
                <?php
                  if(empty($validation['registry']['workload_url'])){
                      echo h($validation['registry']['workload']);
                  } else {
                      echo $this->Html->link(h($validation['registry']['workload']), $validation['registry']['workload_url'], array('target' => '_blank', 'class'=>'link-underlined-nodecoration'));
                  }
                ?>
            </td>
            <?php
                  if(isset($this->data['Filter']['show-platforms'])){
                      $platformIds = array_values($this->data['Filter']['show-platforms']);

                      foreach($platforms as $index=>$platform){
                          $platformindex = $platform['platformversions']['id'];
                          if(in_array($platformindex, $platformIds)){
                              $status = in_array('T'.$platformindex, $validation) ? NULL : $validation['T'.$platformindex]['T'.$platformindex.'_status'];
                              echo '<td style="text-align:center;vertical-align:middle;">';
                              if(is_null($status)){

                                  $link = '';

                                  if (AuthComponent::user('id') && ((AuthComponent::user('role')==='editor' && AuthComponent::user('organizations_id')===$platform['platforms']['organizations_id']) || AuthComponent::user('role')==='admin')) {
                                      $link = $this->Html->link( '+',
                                          array(
                                          'controller' => 'Partners',
                                          'action' => 'validationAdd',
                                          'version' => $validation['registry']['version'],
                                          'protocol' => $validation['registry']['protocol'],
                                          'platformversions_id'=>$platform['platformversions']['id'],
                                          $validation['registry']['product_id']
                                          ));
                                  }
                                  echo '<span style="text-align:center;vertical-align:middle;">' . $link . '</span>';

                              } else {
                                  $level = $validation['T'.$platformindex]['T'.$platformindex.'_level'];
                                  $isCertified = $validation['T'.$platformindex]['T'.$platformindex.'_iscertified'];
                                  $url = $validation['T'.$platformindex]['T'.$platformindex.'_url'];

                                  $ovalStyle = 'background-color:'.$statuses[$status]['color']['back'].'; color:'.$statuses[$status]['color']['fore'].'; border-color:'.$statuses[$status]['color']['border'].';';
                                  echo '<div class="statusoval hasicon" title="'.$statuses[$status]['label'].'" style="'. $ovalStyle . '">';

                                  if(empty($url)){
                                      echo $level;
                                  } else {
                                      echo $this->Html->link($level, $url, array('target' => '_blank', 'class'=>'link-underlined-nodecoration'));
                                  }
                                  if($isCertified == '1'){
                                      echo $certifiedIcon;
                                  }
                                  echo '</div>';
                              }
                              echo '</td>';
                          }
                      }
                  }

                  foreach($organizations as $index=>$organization){
                      echo '<td style="text-align:center;vertical-align:middle;">';
                      echo '<a onclick="showDetail()" href="javascript:void(0);"><u>';
                      echo empty($validation['O'.$index]['O'.$index.'_count'])?"0":$validation['O'.$index]['O'.$index.'_count'];;
                      echo '</u></a>';
                      echo '</td>';
                  }

                  if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
                      echo '<td style="text-align:center;vertical-align:middle;" class="actions">';
                      echo $this->Html->link( 'Edit', array('controller' => 'Partners', 'action' => 'productEdit', $validation['registry']['product_id']) );
                      echo '</td>';
                  }

            ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo (array_key_exists('viewoptions', $this->request->data['Filter']) && in_array('empty', $this->request->data['Filter']['viewoptions'])?'NOTE: Displayed results EXCLUDE ISVs with no validations.':''); ?>
<ul class="paging">
    <?php
    echo $this->Paginator->prev('&laquo;PREV', array( 'tag' => 'li', 'escape' => false), null, array('class' => 'paging prev disabled' ,'tag' => 'li', 'escape' => false));
    echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li' ,'currentClass' => 'current', 'currentTag' => 'a' , 'escape' => false));
    echo $this->Paginator->next('NEXT&raquo;', array( 'tag' => 'li', 'escape' => false), null, array('class' => 'paging next disabled' ,'tag' => 'li', 'escape' => false));
    ?>
</ul>
<br />
<?php
echo $this->Paginator->counter('Page {:page} of {:pages}');
echo ', showing &nbsp;'.$this->Form->input("Filter.limit", array('label'=>false, 'div'=>false, 'options'=>array('10'=>'10', '20'=>'20', '50'=>'50', '100'=>'100'), 'default'=>'20', 'onChange'=>'this.form.submit()'));
echo '&nbsp;'.$this->Paginator->counter('records out of {:count} total, starting on record {:start}, ending on {:end}');
?>

<script>
var timerid;
var delay = 500;

$('#FilterSearchtext').keyup(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationRegistryForm').submit(); }, delay);
});

$(document).ready(function() {
    $('form:first *:input[type!=hidden]:first').focus();
});

</script>

<?php
echo $this->Form->end();
?>

<div id="dialog" title="Validation Details">
    <p>The following validations exist for this partner's product:</p>
    </br>
    <strong>Partner:</strong> Apache</br>
    <strong>Product:</strong> ViewDirect</br>
    <strong>Version:</strong> 6.7</br>
    </br>
    <strong>ETD:</strong></br>
    <table border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td>
                    <strong>ECS 1.0 (ViPR): </strong>
                </td>
                <td>
                    <div title="Completed" class="statusoval hasicon" style="border-color: rgb(0, 153, 51); color: rgb(255, 255, 255); background-color: rgb(0, 153, 51);">CC</div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>ECS 2.0: </strong>
                </td>
                <td>
                    <div title="Completed" class="statusoval hasicon" style="border-color: rgb(0, 153, 51); color: rgb(255, 255, 255); background-color: rgb(0, 153, 51);">CA</div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>ECS 2.2: </strong>
                </td>
                <td>
                    <div title="Completed" class="statusoval hasicon" style="border-color: rgb(0, 153, 51); color: rgb(255, 255, 255); background-color: rgb(0, 153, 51);">CA</div>
                </td>
            </tr>
        </tbody>
    </table>
    <br />
    <strong>CORE:</strong></br>
    --none--</br>
    </br>

</div>
<script>
  $(function() {
      $( "#dialog" ).dialog({autoOpen: false});
  });

  function showDetail(){
      $( "#dialog" ).dialog( "open" );
  }
</script>
