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
				//$('#FilterIndexForm').submit();
			},
		heightStyle: "content",
		collapsible: true,
		create: function(event, ui) { $("#accordion").show(); }
	});
});
</script>

<?php
echo $this->Form->create("Filter");
echo $this->Form->hidden('collapsed');
?>


<?php if(array_key_exists('Filter', $this->request->data) && ! array_key_exists('id', $this->request->data['Filter'])){ ?>


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
                <?php if(array_key_exists('Filter', $this->request->data) && array_key_exists('validations-status', $this->request->data['Filter'])){ ?>
                &nbsp;-&nbsp;Showing&nbsp;Statuses:&nbsp;
            </span>
            <span>
                <?php
                          $separator = '';
                          foreach($this->request->data['Filter']['validations-status'] as $status){
                              echo $separator.'<div class="statusovalsmall" '
                              . 'style="background-color:'.$statuses[$status]['color']['back'].';'
                              . ' color:'.$statuses[$status]['color']['fore'].';'
                              . ' border-color:'.$statuses[$status]['color']['border'].';'
                              . '">&nbsp;</div>&nbsp;'
                              . $statuses[$status]['label'];
                              $separator = ',&nbsp;';
                          }
                ?>
                <?php }?>
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
						echo $this->Form->input('validations-level', array('type'=>'select', 'escape'=>false, 'label'=>array('text'=>'<strong>Validation Levels</strong>', 'escape'=>false), 'div'=>false, 'multiple'=>'checkbox', 'options'=>$options));
                        ?>
                    </td>
                    <td width="33%">
                        <?php
						$options = array();
						foreach($statuses as $key=>$value){
							$options[$key] = '<div class="statusovalsmall" style="background-color:'.$statuses[$key]['color']['back'].'; color:'.$statuses[$key]['color']['fore'].'; border-color:'.$statuses[$key]['color']['border'].';">&nbsp;</div>&nbsp;'.$value['label'];
						}
						echo $this->Form->input('validations-status', array('type'=>'select', 'escape'=>false, 'label'=>array('text'=>'<strong>Available Status Options</strong>', 'escape'=>false), 'div'=>false, 'multiple'=>'checkbox', 'options'=>$options));
                        ?>
                    </td>
                    <td width="33%">
                        <?php echo $this->Form->input('validations-protocol', array('placeholder'=>'Protocol...', 'label'=>'<strong>Protocol</strong>', 'div'=>false)); ?>
                        <br />
                        <br />
                        <?php echo $this->Form->input('platformversions-id', array('type'=>'select', 'label'=>'<strong>EMC Platform</strong>', 'div'=>false, 'options'=>$platformOptions, 'empty'=>Configure::read('Select.emptyOptionText'), 'style'=>'width:100%;')); ?>
                        <br />
                        <br />
                        <?php echo $this->Form->input('validations-sa_owner_id', array('type'=>'select', 'label'=>'<strong>Solution Architect</strong>', 'div'=>false, 'options'=>$sausers, 'empty'=>Configure::read('Select.emptyOptionText'), 'style'=>'width:100%;')); ?>
                    </td>
                </tr>
            </table>
            <?php echo $this->Form->submit('Apply', array('div'=>false)); ?>
        </div>
    </div>
</div>

<?php }?>


<table>
    <thead>
        <tr>
            <th>
                <?php echo $this->Paginator->sort('sa_owner', 'Solutions Architect'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('partners.name', 'Partner'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('products.name', 'Product'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('version', 'Version'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('protocol', 'Protocol'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('validator', 'Validator'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('platform', 'Targeted EMC Technology'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('level', 'Validation Level'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('status', 'Effort Phase'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('estimatedcompletiondate', 'Target Completion Date'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('completiondate', 'Actual Completion Date'); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('notes', 'Notes'); ?>
            </th>
            <?php
			if (AuthComponent::user('id') && (AuthComponent::user('role')==='editor' || AuthComponent::user('role')==='admin')) {
				echo '<th>Actions</th>';
			}
            ?>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($validations as $validation): ?>
        <tr>
            <?php $status = $validation['validations']['status']; ?>
            <td width="200">
                <?php echo h(empty($validation[0]['sa_owner']) ? 'unknown' : $validation[0]['sa_owner']); ?>
            </td>
            <td width="300">
                <?php echo h($validation['partners']['name']); ?>
            </td>
            <td width="400">
                <?php echo h($validation['products']['name']); ?>
            </td>
            <td width="100">
                <?php echo h($validation['validations']['version']); ?>
            </td>
            <td width="100">
                <?php echo h($validation['validations']['protocol']); ?>
            </td>
            <td width="200">
                <?php echo h($validation['validations']['validator']); ?>
            </td>
            <td width="200">
                <?php echo h($validation[0]['platform']); ?>
            </td>
            <td width="150">
                <?php echo $validation['validations']['level']; ?>
            </td>
            <td width="125">
                <div class="statusovallarge" style="background-color:<?php echo $statuses[$status]['color']['back'] ?>; color:<?php echo $statuses[$status]['color']['fore'] ?>; border-color:<?php echo $statuses[$status]['color']['border'] ?>;">
                    <?php echo h($statuses[$status]['label']); ?>
                </div>
            </td>
            <td width="125">
                <?php echo h($this->Time->format('m/Y', $validation['validations']['estimatedcompletiondate'])); ?>
            </td>
            <td width="125">
                <?php echo h($this->Time->format('m/Y', $validation['validations']['completiondate'])); ?>
            </td>
            <td width="500">
                <?php
                  echo h($validation['validations']['notes']);
                ?>
            </td>

            <?php
                  if (AuthComponent::user('id') && ((AuthComponent::user('role')==='editor' && AuthComponent::user('organizations_id')===$validation['platforms']['organizations_id']) || AuthComponent::user('role')==='admin')) {
                      echo '<td  class="actions">';
                      echo $this->Html->link( 'Edit', array('controller' => 'Partners', 'action' => 'validationEdit', $validation['validations']['id']) );
                      echo '</td>';
                  }
            ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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
echo $this->Form->end();
?>

<script>

var timerid;
var delay = 500;

$('#FilterSearchtext').keyup(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterValidationScheduleForm').submit(); }, delay);
});

$(document).ready(function() {
    $('form:first *:input[type!=hidden]:first').focus();
});

</script>
