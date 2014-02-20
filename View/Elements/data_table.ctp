<?php echo $this->Html->script('DataTable.jquery.dataTables'); ?>
<?php echo $this->Html->css('DataTable.datatable'); ?>

<?
    $config = 'model_' . $model;
    $config_index = Inflector::camelize($config);
    Configure::load($config);
    $datatable = Configure::read($config_index);
    $data_url = Router::url(array(
        'plugin' => 'data_table',
        'controller' => 'mongo_tables',
        'action' => 'ajax_get_datatable',
        'model' => $model,
    ))
?>

<? if(!empty($datatable['datatable_options'])): ?>
    <script type="text/javascript">
        var datatable_options = <?= json_encode($datatable['datatable_options']) ?>;
    </script>
<? else: ?>
    <script type="text/javascript">
        var datatable_options = {};
    </script>
<? endif; ?>

<script type="text/javascript">
$(document).ready(function() {
    var defaults = {
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "<?= $data_url ?>",
        "sPaginationType": "full_numbers",
        "aaSorting" : []
    };
    datatable_options = jQuery.extend({}, defaults, datatable_options);
	$('#datatable_<?= $model ?>').dataTable(datatable_options);
	
} );
</script>

<table class="table table-striped table-bordered" id="datatable_<?= $model ?>">
    <thead>
        <tr>
            <? foreach($datatable['fields'] as $field => $options): ?>
                <th class="table-header-repeat line-left minwidth-1">
                    <?
                        if(is_array($options) && isset($options['label'])):
                            echo $options['label'];
                        else:
                            echo $options;
                        endif;
                    ?>
                </th>
            <? endforeach; ?>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

