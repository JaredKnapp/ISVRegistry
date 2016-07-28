<?php 
$this->Html->addCrumb('Partner List', array('controller' => 'partners', 'action' => 'index')); 
$this->Html->addCrumb($partner['Partner']['name'], array('controller'=>'partners', 'action' => 'edit', $partner['Partner']['id'])); 
$this->Html->addCrumb('Add Product');
?>
<div class="products form">
<?php echo $this->Form->create('Product'); ?>
    <fieldset>
        <legend><?php echo __('Add ' . $partner['Partner']['name'] . ' Product'); ?></legend>
        <?php
        echo $this->Form->input('name');
        echo $this->Form->input('url');
        
        $url = $this->Html->url(array('controller' => 'Settings', 'action' => 'getWorkloadsByIndustry'));


        $industrybutton = "";// $this->Form->button('+', array('id'=>'industrypopup', 'class'=>'popuphelper', 'title'=>'Add Industry Choice'));
        echo $this->Form->input('industries_id', array('label'=>'Industry '.$industrybutton, 'id' => 'industries', 'empty' => '(choose one...)', 'rel'=>$url));
        $workloadbutton = "";//$this->Form->button('+', array('id'=>'workloadpopup', 'class'=>'popuphelper', 'title'=>'Add Workload Choice'));
        echo $this->Form->input('workloads_id', array('label'=>'Workload '.$workloadbutton, 'id' => 'workloads', 'empty' => '(choose one...)'));

        echo $this->Form->input('sa_owner_id', array('label'=>'Solution Architect', 'type' => 'select', 'options' => $sausers, 'empty' => '(choose one...)'));
        echo $this->Form->input('ba_owner_id', array('label'=>'Business Development', 'type' => 'select', 'options' => $bausers, 'empty' => '(choose one...)'));
        ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<script>
function updateChoices(s){
	var selectedValue = s.val();
	var targeturl = s.attr('rel') + '/' + selectedValue;
	$.ajax({
		type: 'get',
		url: targeturl,
		beforeSend: function(r) {
			r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		},
		success: function(r) {
			if (r) {
				$('#workloads').html(r);
			}
		},
		error: function(e) {
			alert("An error occurred: " + e.responseText.message);
			console.log(e);
		}
	});
}

$(function() {
	$('#industries').change(function() { updateChoices($(this)); });
	$( document ).ready(function() { updateChoices($('#industries')); });
});


function addIndustry() {
    var valid = true;
    allFields.removeClass( "ui-state-error" );
 
//    valid = valid && checkLength( name, "username", 3, 16 );
//    valid = valid && checkLength( email, "email", 6, 80 );
//    valid = valid && checkLength( password, "password", 5, 16 );
 
//    valid = valid && checkRegexp( name, /^[a-z]([0-9a-z_\s])+$/i, "Username may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
//    valid = valid && checkRegexp( email, emailRegex, "eg. ui@jquery.com" );
//    valid = valid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
 
//    if ( valid ) {
//    $( "#users tbody" ).append( "<tr>" +
//        "<td>" + name.val() + "</td>" +
//        "<td>" + email.val() + "</td>" +
//        "<td>" + password.val() + "</td>" +
//    "</tr>" );
    dialog.dialog( "close" );
//    }
    return valid;
}
	
dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Create Industry Record": addIndustry,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "ui-state-error" );
      }
    });

$( "#industrypopup" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });
	
$( "#workloadpopup" ).button().on( "click", function() {
      dialog.dialog( "open" );
    });
</script>
<!--
<div id="dialog-form" title="Add Industry">
  <form>
    <fieldset>
      <label for="name">Name</label>
      <input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all">
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
-->