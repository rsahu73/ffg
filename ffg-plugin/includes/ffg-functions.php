<?php 

add_action('admin_menu', 'ffg_admin_page_menu');
add_action('xprofile_updated_profile', 'ffg_save_user_persona');
add_filter('bp_get_the_profile_field_required_label', 'ffg_change_required_label', 10, 2);
add_filter('bp_after_has_profile_parse_args', 'hide_persona_fields');
//add_action( 'wp_login', 'ffg_on_login_check' , 0 );
//add_action('xprofile_updated_profile','ffg_check_for_required_fields' );


// function ffg_on_login_check($user_login) {
//     $user = get_user_by( 'login', $user_login );
//     if ( ! $user ) {
//         return;
//     }
//     $redirect_url = bp_core_get_user_domain( $user->ID ) . bp_get_profile_slug().'/edit/';
//     bp_core_add_message(__( 'Your Profile is Incomplete. Please complete your profilr', 'ffg' ), 'error' );
//     $is_complete = ffg_check_for_profile_complete($user->ID);
//     if (!$is_complete) {
//         bp_core_redirect($redirect_url);
//         bp_core_add_message('Your Profile is Incomplete. Please complete your profilr', 'error' );
//     }
    
// }

// function ffg_check_for_profile_complete($user_id) {
//     global $wpdb;
//     $complete = true;
//     $field_table = buddypress()->profile->table_name_fields;

//     $field_query = $wpdb->prepare( "SELECT * FROM {$field_table} WHERE is_required = %d", 1 );
//     $required_fields_result = $wpdb->get_results( $field_query );
//     $required_field_array = array();
//     foreach($required_fields_result as $field) {
//         array_push($required_field_array, $field->id);
//     }
//     update_option('ffg_required_fields', implode(',',$required_field_array));
//     $required_fields = implode(',',$required_field_array);

//     $data_table = buddypress()->profile->table_name_data;
//     $data_query = $wpdb->prepare( "SELECT *  FROM {$data_table} WHERE user_id = %d AND field_id IN ( {$required_fields} ) GROUP BY field_id", $user_id );


//     $profile_data = $wpdb->get_results($data_query);
//     $profile_data_arr = array();
//     foreach($profile_data as $data_field) {
//         array_push($profile_data_arr, $data_field->field_id);
//     }

//     $empty_required_field = array();
//     if (count($profile_data) != count($required_field_array)) {
//         $complete = false;
//         foreach ($required_field_array as $field_arr) {
//             if (!in_array($field_arr, $profile_data_arr, true))  {
//                 array_push($empty_required_field, $field_arr);
//             }
//         }
//     }
//     else {
//         foreach ($profile_data as $data) {
//             $value = maybe_unserialize( $data->value );
//             if (empty($value)) {
//                 $complete = false;
//             }
//         }
//     }
//     update_user_meta($user_id, 'ffg_incomplete_fields', ($complete ? 'True : ' : 'False : ') . implode(',',$empty_required_field));
//     return $complete;

// }

function hide_persona_fields($retval) {
    if(  bp_is_user_profile_edit() ) {  
        $persona_field_id = xprofile_get_field_id_from_name('Persona');
        $persona_overview_field_id = xprofile_get_field_id_from_name('Persona Overview');

        $retval['exclude_fields'] = $persona_field_id.','.$persona_overview_field_id;
    }
    else {
        $retval['exclude_fields'] = '';
    }
    return $retval;
}

function ffg_change_required_label() {
    return "*";
}


function ffg_save_user_persona() {
    $meta_key = 'ffg-user-persona-value';
    $user_id = bp_displayed_user_id();
    
    $persona_calculated_value = array('P'=>0, 'E'=>0, 'C'=>0);
    $persona_fields = ffg_get_persona_fields();

    if (!empty($persona_fields)) {
        foreach($persona_fields as $field) {
            $field_data_values = xprofile_get_field_data($field->id, $user_id);
            if (!empty($field_data_values)) {
                if (!is_array($field_data_values)) {
                    $field_data_array = array($field_data_values);
                }
                else {
                    $field_data_array = $field_data_values;
                }
                $persona_calculated_array = array('P'=>0, 'E'=>0, 'C'=>0);
                $weight_per_option_meta_value = bp_xprofile_get_meta( $field->id, 'field', 'ffg-persona-weight-per-option' );

                $fetched_meta_value = bp_xprofile_get_meta( $field->id, 'field', 'ffg-persona-mapping-meta' );
                if (is_array($fetched_meta_value)){
                    $saved_meta_value = $fetched_meta_value;
                }
                else {
                    $saved_meta_value = unserialize($fetched_meta_value);
                }
                $total_meta_count = 0;
                foreach($saved_meta_value as $key=>$value){
                    if (ffg_validate_meta_data($value)){
                        $total_meta_count++;
                    }
                }
                echo "Meta count provided : ".$total_meta_count;
                $weight_per_option_meta_value = number_format($weight_per_option_meta_value/$total_meta_count,2);

                foreach($field_data_array as $field_data) {

                    // Old Logic
                    //$p_persona_calculated_array = ffg_calculate_user_persona($field_data, $field->id, $persona_calculated_array);                    
                    // $saved_meta_value = unserialize(bp_xprofile_get_meta( $field->id, 'field', 'ffg-persona-mapping-meta' ));
                    // $persona_map_value = explode("/",$saved_meta_value[$field_data]);
                
                    // if (in_array('P', $persona_map_value)) {
                    //     $p_persona_calculated_array['P'] = $p_persona_calculated_array['P'] + 1;
                    // }
                    // if (in_array('E', $persona_map_value)) {
                    //     $p_persona_calculated_array['E'] = $p_persona_calculated_array['E'] + 1;
                    // }
                    // if (in_array('C', $persona_map_value)) {
                    //     $p_persona_calculated_array['C'] = $p_persona_calculated_array['C'] + 1;
                    // }

                    // New Logic
                    $persona_map_value = explode("/",$saved_meta_value[$field_data]);
                    $per_option_weight = number_format($weight_per_option_meta_value / count($persona_map_value),2);
                    if (in_array('P', $persona_map_value)) {
                        $persona_calculated_value['P'] = $persona_calculated_value['P'] + $per_option_weight;
                    }
                    if (in_array('E', $persona_map_value)) {
                        $persona_calculated_value['E'] = $persona_calculated_value['E'] + $per_option_weight;
                    }
                    if (in_array('C', $persona_map_value)) {
                        $persona_calculated_value['C'] = $persona_calculated_value['C'] + $per_option_weight;
                    }
               }
                // $persona_calculated_value = ffg_calculate_user_persona_value($p_persona_calculated_array, $persona_calculated_value); 
                // $p_persona_calculated_array['P'] = 0;
                // $p_persona_calculated_array['E'] = 0;
                // $p_persona_calculated_array['C'] = 0;
            }
        }
        bp_update_user_meta( $user_id, 'ffg-user-persona-value', serialize($persona_calculated_value) );
        bp_update_user_meta( $user_id, 'ffg-user-persona-partner-value', $persona_calculated_value['P'] );
        bp_update_user_meta( $user_id, 'ffg-user-persona-entrepreneur-value', $persona_calculated_value['E'] );
        bp_update_user_meta( $user_id, 'ffg-user-persona-citizen-value', $persona_calculated_value['C'] );
        bp_update_user_meta( $user_id, 'ffg-user-persona', ffg_get_user_persona($persona_calculated_value) );
        ffg_set_persona_fields($user_id, $persona_calculated_value);
    }

}

function ffg_calculate_user_persona_value(array $p_persona_calculated_array, array &$persona_calculated_value) {
    $sum = 0;
    foreach ($p_persona_calculated_array as $key=>$value){
        $sum = $sum + $value;
    }

    $persona_calculated_value['P'] = $persona_calculated_value['P'] + number_format(($p_persona_calculated_array['P'] / $sum), 1);
    $persona_calculated_value['E'] = $persona_calculated_value['E'] + number_format(($p_persona_calculated_array['E'] / $sum), 1);
    $persona_calculated_value['C'] = $persona_calculated_value['C'] + number_format(($p_persona_calculated_array['C'] / $sum), 1);

    return $persona_calculated_value;

}

function ffg_set_persona_fields($user_id, array $persona_calculated_value) {
    xprofile_set_field_data('Persona', $user_id, ffg_get_user_persona($persona_calculated_value));
    $total = $persona_calculated_value['P'] + $persona_calculated_value['E'] + $persona_calculated_value['C'];
    $partner_per = number_format(($persona_calculated_value['P']/$total)*100, 1);
    $entrepreneur_per = number_format(($persona_calculated_value['E']/$total)*100, 1);
    $citizen_per = number_format(($persona_calculated_value['C']/$total)*100, 1);
    $persona_overview = "Partner : " . $partner_per . " % | Entrepreneur : " . $entrepreneur_per . " % | Citizen : " . $citizen_per . " %";
    xprofile_set_field_data('Persona Overview', $user_id, $persona_overview);
}

function ffg_get_user_persona(array $p_persona_calculated_array) {
    $tempvalue = 0;
    $temppersona = 'NA';

    if (!empty($p_persona_calculated_array)) {

        foreach ($p_persona_calculated_array as $key=>$value) {
            if($value >= $tempvalue) {
                $tempvalue = $value;
                $temppersona = $key;
            }
        }
    }
    switch ($temppersona) {
        case 'P' : 
            return "Partner";
        case 'E' : 
            return "Entrepreneur";
        case 'C' : 
            return "Citizen";
        default : 
            return 'NA';
    }
}

function ffg_calculate_user_persona($p_field_data, $p_field_id, &$p_persona_calculated_array) {
    $meta_value = bp_xprofile_get_meta( $p_field_id, 'field', 'ffg-persona-mapping-meta' );
    $saved_meta_value = unserialize($meta_value);
    $persona_map_value = explode("/",$saved_meta_value[$p_field_data]);

    if (in_array('P', $persona_map_value)) {
        $p_persona_calculated_array['P'] = $p_persona_calculated_array['P'] + 1;
    }
    if (in_array('E', $persona_map_value)) {
        $p_persona_calculated_array['E'] = $p_persona_calculated_array['E'] + 1;
    }
    if (in_array('C', $persona_map_value)) {
        $p_persona_calculated_array['C'] = $p_persona_calculated_array['C'] + 1;
    }

    return $p_persona_calculated_array;

}

function ffg_admin_page_menu() {

    add_menu_page(
        'FFG Custom Settings',
        'FFG Custom Settings',
        'manage_options',
        'ffg-setting',
        'ffg_setting_page_content',
        'dashicons-schedule'
    );
}

function ffg_get_persona_fields() {
    global $wpdb;
    return $wpdb->get_results( "SELECT f.* FROM `wp_bp_xprofile_fields` f , `wp_bp_xprofile_groups` g
    where f.group_id = g.id
    and g.name = 'Persona'
    and parent_id = 0;");
}

function ffg_get_field_options($field_id) {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM `wp_bp_xprofile_fields`
    where type='option'
    and parent_id = ".$field_id.";");
}

function ffg_validate_meta_data($meta_data) {
    return preg_match("/[PpeECc\/]{1,5}/i", $meta_data);
}

function ffg_setting_page_content() {
    global $wpdb;
    
    $results = ffg_get_persona_fields();    
    ?>
    <h1>
        <?php esc_html_e('Welcome to FFG Custom Settings page.' , 'ffg-settings-page'); ?>
    </h1>
    <?php 
        if (!empty($_POST)) {
            if (!empty($results)) {
                foreach($results as $row) {
                    $fieldMeta = array();
                    $tempData = "";
                    $options = ffg_get_field_options($row->id);
                    if (!empty($options)) {
                        foreach($options as $childRow) {
                            //$tempData = $tempData.$childRow->id."#".$_POST[$childRow->id];
                                $fieldMeta[$childRow->name]=$_POST[$childRow->id];
                        }                        
                    }
                    bp_xprofile_update_field_meta( $row->id, 'ffg-persona-mapping-meta', serialize($fieldMeta) );
                    bp_xprofile_update_field_meta( $row->id, 'ffg-persona-weight-per-option', $_POST['weight-'.$row->id] );
                }
            }
            echo "Data Saved Successfully";
        }
    ?>
    <form name="persona_mapping" action="" method="post">
        <br/>
        <h3>Persona Mapping fields</h3>
    <?php


        if (!empty($results)) {
            echo "<table>";
            foreach($results as $row) {
                $saved_weight_value = bp_xprofile_get_meta( $row->id, 'field', 'ffg-persona-weight-per-option' );
                echo "<tr><td colspan=2><h4>".$row->name."&nbsp;&nbsp;<input type='text' size=12 placeholder='Point per Option' name='weight-".$row->id."' value='".$saved_weight_value."'></h4></td></tr>";
                $options = $wpdb->get_results("SELECT * FROM `wp_bp_xprofile_fields`
                where type='option'
                and parent_id = ".$row->id.";");
                $saved_meta_array = array();
                $saved_meta_value = bp_xprofile_get_meta( $row->id, 'field', 'ffg-persona-mapping-meta' );
                $saved_meta_array = unserialize($saved_meta_value);
                //print_r($saved_meta_array);
                if (!empty($options)) {

                    foreach($options as $childRow) {
                        echo "<tr><td><label>".$childRow->name."</label></td>";
                        echo "<td><input type='text' size=10 placeholder='Persona Values' name='".$childRow->id."' value='".$saved_meta_array[$childRow->name]."'></td></tr>";
                        echo "<tr></tr>";
                    }
                }
            }
        }
    ?>
    <tr></tr>
    <tr><td></td><td><input type="submit" value="Save"></td>
    </table>
    </form>
    <?php
    
        
}

