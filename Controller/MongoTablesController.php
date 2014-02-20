<?php
App::uses('DataTableAppController', 'DataTable.Controller');
/**
 * MongoTables Controller
 *
 */
class MongoTablesController extends DataTableAppController {

    function ajax_get_datatable() {
        
        $this->layout = null;
        if(empty($this->request->named['model'])) {
            return;
        }
        $model = $this->request->named['model'];
        
        $this->loadModel($model);
        
        $config = 'model_' . Inflector::slug($model);
        $config_index = Inflector::camelize($config);
        Configure::load($config);
        $datatable = Configure::read($config_index);
        $options = $this->request->query;
        $fields = $datatable['fields'];
        $field_keys = array_keys($fields);

        $op = array();
        if(!empty($datatable['conditions'])) {
            $op['conditions'] = $datatable['conditions'];
        }
        
        // Search by Options
        $search_columns = $datatable['search_fields'];
        if(!empty($options['sSearch'])) {
            foreach($search_columns as $column) {
                $op['conditions']['$or'][] = array(
                    $column => array(
                        '$regex' => '.*' . $options['sSearch'] . '.*',
                        '$options' => 'i',
                    )
                );
            }
        }


        $result['iTotalRecords'] = $this->{$model}->find('count',$op);
        $result['iTotalDisplayRecords'] = $result['iTotalRecords'];

	
        // Sorting by Options
        for($i=0 ; $i < intval($options['iSortingCols']); $i++ ) {
            $column_index = intval($options['iSortCol_'.$i]);
            $sort_direction = $options["sSortDir_$i"];
            $sort_field = $field_keys[$column_index];
            $op['order'][$sort_field] = $sort_direction;
        }
        
        if(empty($op['order'])) {
            $op['order'] = $datatable['order'];
        }
        
        $op['limit'] =$options['iDisplayLength'];
        $op['offset']= $options['iDisplayStart'];
        $items = $this->{$model}->find('all', $op);
        
        $result['sEcho'] = $options['sEcho'];
        $result['aaData'] = array();
        foreach($items as $item) {
            $row = array();
            foreach($fields as $field => $options) {
                if(isset($item[$model][$field])) {
                    if(is_array($options) && !empty($options['options'][@$item[$model][$field]])) {
                        $row[] = @$options['options'][@$item[$model][$field]];
                    }
                    else if(is_array($options) && !empty($options['display_format'])) {
                        $text = str_replace('FIELD_VALUE', @$item[$model][$field], $options['display_format']);
                        if(!empty($item[$model]['_id'])) {
                            $text = str_replace('ID_VALUE', $item[$model]['_id'], $text);
                        }
                        elseif(!empty($item[$model]['id'])) {
                            $text = str_replace('ID_VALUE', $item[$model]['id'], $text);
                        }
                        $row[] = $text;
                    }
                    else if(is_array($options) && !empty($options['link_format'])) {
                        $url = str_replace('FIELD_VALUE', @$item[$model][$field], $options['link_format']);
                        if(!empty($item[$model]['_id'])) {
                            $url = str_replace('ID_VALUE', $item[$model]['_id'], $url);
                        }
                        elseif(!empty($item[$model]['id'])) {
                            $url = str_replace('ID_VALUE', $item[$model]['id'], $url);
                        }
                        $row[] = '<a href="' . Router::url($url) . '">' . @$item[$model][$field] . '</a>' ;
                    }
                    else if($field == 'created' && isset($item[$model][$field]->sec)) {
                        $row[] = date('d/m/Y H:i', $item[$model][$field]->sec);
                    }
                    else {
                        $row[] = $item[$model][$field];
                    }
                }
                else {
                    $row[] = '';
                }
            }
            $result['aaData'][] = $row;
        }

        $this->set('data', $result);
    }

}
