<?php
/**
 * Created by PhpStorm.
 * User: mytag
 * Date: 17.08.2021
 * Time: 12:04
 */

add_action('admin_menu', 'start_info_page', 25);

function start_info_page()
{

    add_menu_page(
        __('Połączenia użytkownikow', 'cc'),
        'Połączenia użytkownikow',
        'manage_options',
        'usr-info',
        'setting_view',
        'dashicons-universal-access',
        20
    );
}

add_action('admin_enqueue_scripts', 'stili_backend', 25);

function stili_backend()
{
    if ($_GET['page'] == 'usr-info'):
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css');
        wp_enqueue_style('admin_styles', plugins_url('/client-cart/admin/assets/css/main.css'));
        wp_enqueue_script('bsjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js');
        wp_enqueue_script('input', plugins_url('/client-cart/admin/assets/js/input.js'));

    endif;
}

function setting_view()
{
    return include __DIR__ . '/backend-view.php';
}


function backend_have_subscribers()
{
    $users = get_all_users();
    $arr = [];

    foreach ($users as $field => $user) {
        $role = get_userdata($user->ID)->roles;
        $userRole = $role;
        if (in_array('realizator_new', $userRole)):
            $arr[$field]['id'] .= $user->ID;
            $arr[$field]['name'] .= $user->display_name;
        endif;
    }
    return $arr;
}

////begin import
function import_csv($file_path, $file_encodings = ['UTF-8', 'UTF-8'], $col_delimiter = ';', $row_delimiter = "")
{
    $cont = trim(file_get_contents($file_path));
    $encoded_cont = mb_convert_encoding($cont, 'UTF-8', mb_detect_encoding($cont, $file_encodings));
    unset($cont);
    if (!$row_delimiter) {
        $row_delimiter = "\r\n";
        if (false === strpos($encoded_cont, "\r\n"))
            $row_delimiter = "\n";
    }

    $lines = explode($row_delimiter, trim($encoded_cont));
    $lines = array_filter($lines);
    $lines = array_map('trim', $lines);
    if (!$col_delimiter) {
        $lines10 = array_slice($lines, 0, 30);

        foreach ($lines10 as $line) {
            if (!strpos($line, ',')) $col_delimiter = ';';
            if (!strpos($line, ';')) $col_delimiter = ',';

            if ($col_delimiter) break;
        }
        if (!$col_delimiter) {
            $delim_counts = array(';' => array(), ',' => array());
            foreach ($lines10 as $line) {
                $delim_counts[','][] = substr_count($line, ',');
                $delim_counts[';'][] = substr_count($line, ';');
            }
            $delim_counts = array_map('array_filter', $delim_counts); // уберем нули
            $delim_counts = array_map('array_count_values', $delim_counts);

            $delim_counts = array_map('max', $delim_counts); // берем только макс. значения вхождений

            if ($delim_counts[';'] === $delim_counts[','])
                return array('Dont  have a ";"');
            $col_delimiter = array_search(max($delim_counts), $delim_counts);
        }

    }
    $data = [];
    foreach ($lines as $key => $line) {
        $data[$key] = str_getcsv($line, $col_delimiter); // linedata
        unset($lines[$key]);
    }
    return $data;
}

//end import;

//try reading
function create_csv_file($create_data, $file = null, $col_delimiter = ';', $row_delimiter = "\r\n")
{

    if (!is_array($create_data))
        return false;

    if ($file && !is_dir(dirname($file)))
        return false;
    $CSV_str = '';

    // перебираем все данные
    foreach ($create_data as $row) {
        $cols = array();
        foreach ($row as $col_val) {
            if ($col_val && preg_match('/[",;\r\n]/', $col_val)) {
                // поправим перенос строки
                if ($row_delimiter === "\r\n") {
                    $col_val = str_replace("\r\n", '\n', $col_val);
                    $col_val = str_replace("\r", '', $col_val);
                } elseif ($row_delimiter === "\n") {
                    $col_val = str_replace("\n", '\r', $col_val);
                    $col_val = str_replace("\r\r", '\r', $col_val);
                }
                $col_val = str_replace('"', '""', $col_val); // предваряем "
                $col_val = '"' . $col_val . '"';
            }

            $cols[] = $col_val;
        }

        $CSV_str .= implode($col_delimiter, $cols) . $row_delimiter;
    }

    $CSV_str = rtrim($CSV_str, $row_delimiter);
    if ($file) {
        $CSV_str = iconv("UTF-8", "UTF-8", $CSV_str);
        $done = file_put_contents($file, $CSV_str);
        return $done ? $CSV_str : false;
    }

    return $CSV_str;
}


function insert_to_database()
{
    global $wpdb;
    if ($_FILES && $_FILES["import"]["error"] == UPLOAD_ERR_OK):
        $directory = __DIR__ . '/csv/';
        $extension = '.csv';
        move_uploaded_file($_FILES["import"]["tmp_name"], $directory . 'data' . $extension);
        $import_array = import_csv(plugins_url('/client-cart/admin/csv/data.csv'));
        $data = [];
        foreach ($import_array as $key => $line):
            //11  awesome vars  #@*$#@%%^*
            $data[$key]['m_user_id'] .= $line[0];
            $data[$key]['name'] .= $line[1];
            $data[$key]['m_user_email'] .= $line[2];
            $data[$key]['mpk_id'] .= $line[3];
            $data[$key]['mpk_address'] .= $line[4];
            $data[$key]['sub_user_id'] .= $line[5];
            $data[$key]['sub_user_email'] .= $line[6];
            $data[$key]['m_user_role_adm'] .= $line[7];
            $data[$key]['m_user_role_akcept'] .= $line[8];
            $data[$key]['m_user_role_real'] .= $line[9];
            $data[$key]['m_user_role_client'] .= $line[10];
        endforeach;
        foreach ($data as $key => $line):
            $m_user_id = trim($line['m_user_id']);
            $m_user_name = trim($line['name']);
            $m_user_email = trim($line['m_user_email']);
            $mpk_id = trim($line['mpk_id']);
            $mpk_address = trim($line['mpk_address']);
            $subUserId = trim($line['sub_user_id']);
            $subUserEmail = trim($line['sub_user_email']);
            $roleAdmin = trim($line['m_user_role_adm']);
            $roleAkcept = trim($line['m_user_role_akcept']);
            $roleReal = trim($line['m_user_role_real']);
            $roleClient = trim($line['m_user_role_client']);
            $checkUserId = $wpdb->get_results("SELECT ID FROM wp_users WHERE ID = $$m_user_id");
            $checkSubscriberID = $wpdb->get_results("SELECT ID FROM wp_users WHERE ID = $subUserId");
            if ($checkUserId == null):
                $wpdb->insert('wp_users', [
                    'ID' => $m_user_id,
                    'user_login' => $m_user_name,
                    'user_pass' => 'e807f1fcf82d132f9bb018ca6738a19f',
                    'user_nicename' => $m_user_name,
                    'user_email' => $m_user_email,
                    'display_name' => $m_user_name
                ], ['%s']);
            endif;
            if ($checkSubscriberID == null):
                $wpdb->insert('wp_users', [
                    'ID' => $subUserId,
                    'user_login' => $subUserEmail,
                    'user_pass' => 'e807f1fcf82d132f9bb018ca6738a19f',
                    'user_nicename' => 'New_imported_user' . $key,
                    'user_email' => $subUserEmail,
                    'display_name' => 'New_imported_user' . $key
                ], ['%s']);
            endif;
            $checkRelation = $wpdb->get_results("SELECT 'user_id','subscriber_id' FROM wp_subscribers WHERE user_id = $m_user_id AND subscriber_id = $subUserId ");
            if ($checkRelation != null):
                $wpdb->update('wp_subscribers', ['user_id' => $m_user_id, 'subscriber_id' => $subUserId], ['user_id' => $m_user_id, 'subscriber_id' => $subUserId]);
            else:
                //if on base return  null create relation (is so important)
                $wpdb->insert('wp_subscribers', ['user_id' => $m_user_id, 'subscriber_id' => $subUserId], ['%s']);
            endif;
            if ($roleAdmin == 1):
                $u = new WP_User($m_user_id);
                $u->add_role('administrator');
                if ($roleAkcept == 1):
                    $u->set_role('b2bking_role_1173');
                elseif ($roleReal == 1):
                    $u->set_role('realizator_new');
                elseif ($roleClient == 1):
                    $u->set_role('customer');
                endif;
                // update or create mpk field
                if ($mpk_id == null):
                    update_user_meta($m_user_id, 'mpk', 'Niestety adres nie został wprowadzony podczas importu');
                else:
                    update_user_meta($m_user_id, 'mpk', $mpk_id . '(' . $mpk_address . ')');
                endif;
            endif;
        endforeach;
    endif;
}


function what_in_array()
{
    $directory = __DIR__ . '/csv/';
    $extension = '.csv';
    move_uploaded_file($_FILES["import"]["tmp_name"], $directory . 'data' . $extension);
    $import_array = import_csv(plugins_url('/client-cart/admin/csv/data.csv'));
    return $import_array;
}