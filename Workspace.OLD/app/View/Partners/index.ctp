<?php $this->Html->addCrumb('Partner List'); ?>

<?php echo $this->Html->link('Add New ISV',array('controller' => 'Partners', 'action' => 'add')); ?><br>
<br>
<?php 
$base_url = array('controller' => 'Partners', 'action' => 'index');
echo $this->Form->create("Filter",array('url' => $base_url, 'class' => 'filter'));
echo $this->Form->text("Partner-name", array('placeholder' => "Search for Partner...", 'default' => '', 'style'=>'width:250px;'));
echo $this->Form->end();
?>
<table>
    <tr>
        <th>
            <?php echo $this->Paginator->sort('name', 'Name'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('url', 'URL'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('SaOwner.firstname', 'Solution Architect'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('BaOwner.firstname', 'Business Owner'); ?>
        </th>
        <th>Actions</th>
    </tr>
    <?php foreach ($partners as $partner): ?>
	<tr>
	<td>
            <?php echo h($partner['Partner']['name']); ?>
</td>
<td>
            <?php echo h($partner['Partner']['url']); ?>
</td>
<td>
            <?php echo h($partner['SaOwner']['firstname']). ' ' . h($partner['SaOwner']['lastname']); ?>
</td>
<td>
            <?php echo h($partner['BaOwner']['firstname']). ' ' . h($partner['BaOwner']['lastname']); ?>
</td>
<td class='actions'>
            <?php
            echo $this->Html->link( 'Edit', array('action' => 'edit', $partner['Partner']['id']) );
            echo $this->Form->postLink( 'Delete', array('action' => 'delete', $partner['Partner']['id']), array('confirm'=>'Are you sure you want to delete \''.$partner['Partner']['name'].'\'?') );
            ?>
</td>
</tr>
    <?php endforeach; ?>
</table>

<ul class="paging">
<?php 
echo $this->Paginator->prev('&laquo;PREV', array( 'tag' => 'li', 'escape' => false), null, array('class' => 'paging prev disabled' ,'tag' => 'li', 'escape' => false));
echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li' ,'currentClass' => 'current', 'currentTag' => 'a' , 'escape' => false));
echo $this->Paginator->next('NEXT&raquo;', array( 'tag' => 'li', 'escape' => false), null, array('class' => 'paging next disabled' ,'tag' => 'li', 'escape' => false));
?>
</ul>
<br>
<?php
echo $this->Paginator->counter('Page {:page} of {:pages}');
echo ', showing &nbsp;'.$this->Form->input("Filter.limit", array('label'=>false, 'div'=>false, 'options'=>array('10'=>'10', '20'=>'20', '50'=>'50', '100'=>'100'), 'default'=>'20', 'onChange'=>'this.form.submit()'));
echo '&nbsp;'.$this->Paginator->counter('records out of {:count} total, starting on record {:start}, ending on {:end}');
?> 

<script>
var timerid;
var delay = 500;
$('#FilterIndexForm :input').keyup(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterIndexForm').submit(); }, delay);
});
</script>
