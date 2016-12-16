<?php echo $this->Html->link('Add New User',array('controller' => 'Users', 'action' => 'add')); ?>
<br />
<?php
$base_url = array('controller' => 'Users', 'action' => 'index');
echo $this->Form->create("Filter",array('url' => $base_url, 'class' => 'filter'));
echo $this->Form->text("fullName", array('placeholder' => "Search by Full Name...", 'default' => '', 'style'=>'width:250px;'));
echo $this->Form->end();
?>
<table>
    <tr>
        <th>
            <?php echo $this->Paginator->sort('firstname', 'First Name'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('lastname', 'Last Name'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('is_sa', 'Solution Architect'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('is_ba', 'Business Development'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('organization', 'Organization'); ?>
        </th>
        <th>
            <?php echo $this->Paginator->sort('email', 'Email'); ?>
        </th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td>
            <?php echo h($user['User']['firstname']); ?>
        </td>
        <td>
            <?php echo h($user['User']['lastname']); ?>
        </td>
        <td>
            <?php echo h($user['User']['is_sa']==1?'Yes':''); ?>
        </td>
        <td>
            <?php echo h($user['User']['is_ba']==1?'Yes':''); ?>
        </td>
        <td>
            <?php echo h($user['Organization']['name']); ?>
        </td>
        <td>
            <?php echo h($user['User']['email']); ?>
        </td>
        <td class='actions'>
            <?php
              echo $this->Html->link( 'Edit', array('action' => 'edit', $user['User']['id']) );
              echo $this->Form->postLink( 'Delete', array('action' => 'delete', $user['User']['id']), array('confirm'=>'Are you sure you want to delete \''.$user['User']['email'].'\'?') );
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
<br />
<?php
echo $this->Paginator->counter('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}');
?>
<script>
var timerid;
var delay = 1000;
$('#FilterIndexForm :input').keyup(function() {
  var form = this;
  clearTimeout(timerid);
  timerid = setTimeout(function() { $('#FilterIndexForm').submit(); }, delay);
});
</script>
