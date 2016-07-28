<div class="platforms form">
    <?php echo $this->Form->create('Industry'); ?>
    <fieldset>
        <legend>
            <?php echo __('Edit Industry'); ?>
        </legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('url');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
    <h1>Available Workloads</h1>
    <?php echo $this->Html->link('Add New Workload',array('action' => 'workloadAdd', $this->request->data['Industry']['id'])); ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Url</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->request->data['Workloads'] as $workload): ?>
            <tr>
                <td>
                    <?php echo h($workload['name']); ?>
                </td>
                <td>
                    <?php echo h($workload['url']); ?>
                </td>
                <td class='actions'>
                    <?php
                      echo $this->Html->link( 'Edit', array('action' => 'workloadEdit', $workload['id']) );
                      echo $this->Form->postLink( 'Delete', array('action' => 'workloadDelete', $workload['id']), array('confirm'=>'Are you sure you want to delete workload \''.$workload['name'].'\'?') );
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
