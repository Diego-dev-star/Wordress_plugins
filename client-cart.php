<?php
/*
Plugin Name: client's cart
Description:
Author: Vitali B for Pageart TM
Version: 0.7.2
*/

//here as including  admin functions
require_once __DIR__ .'/admin/backend.php';
//end this



//this a link for account

include('database/subscribers.php');
function cart_enqueue_style()
{
    wp_enqueue_style('modal', plugins_url('assets/modal/modal.css', __FILE__), false);
    wp_enqueue_script('livesearch', plugins_url('client-cart/assets/user-list/jquery.chained.js'), array('jquery'));
    wp_enqueue_script('main', plugins_url('client-cart/assets/main.js'), array('jquery'));


}


add_action('wp_enqueue_scripts', 'cart_enqueue_style');
function catalog($menu_links)
{
    $user_id = get_current_user_id();
    $user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles;
    if (in_array('realizator_new', $user_roles)) :
        $menu_links['client-cart'] = __('Clients table');
        $menu_links['clients'] = __('All user');
        $menu_links = array_slice($menu_links, 0, 5, true)
            + array('client-cart' => 'Panel akceptacji', 'clients' => 'Wszyscy użytkownicy')
            + array_slice($menu_links, 5, NULL, true);
        return $menu_links;
    else:
        return $menu_links;
    endif;
}

add_filter('woocommerce_account_menu_items', 'catalog', 25);


function page_add()
{
    add_rewrite_endpoint('client-cart', EP_PAGES);
    add_rewrite_endpoint('order', EP_PAGES);
    add_rewrite_endpoint('clients', EP_PAGES);
}

add_action('init', 'page_add', 25);


function page_content()
{
    include 'view/table.php';
}

add_action('woocommerce_account_client-cart_endpoint', 'page_content', 25);

function user_order_view()
{
    include 'view/order.php';

}

add_action('woocommerce_account_order_endpoint', 'user_order_view', 25);


function all_users_getting()
{
    include 'view/users.php';
}

add_action('woocommerce_account_clients_endpoint', 'all_users_getting', 25);


function get_all_users()
{
    $user = get_users();
    return $user;


}

function get_user_roles($user_id)
{
    $user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles;
    if (in_array('b2bking_role_1173', $user_roles)):
        return 'Realizator';
    endif;
}


//getting order's

function get_order_view()
{
    return plugin_dir_url(__FILE__) . 'view/oreder.php';
}


function get_order_value_status()
{
    $statuses = array(
        'wc-processing' => 'W realizacji',
        'wc-accept-status' => 'Zaakceptowane',
        'wc-shipment-status' => 'Dostarczone',
        'wc-completed' => 'Zrealizowane',
        'wc-cancelled' => 'Odrzucone',
        'wc-waiting' => 'Oczekuje na akceptację',
    );
    return $statuses;
}

function translate_order_status($ordrer_status)
{
    if ($ordrer_status == 'waiting'):
        return 'Oczekuje na akceptację ';
    elseif ($ordrer_status == 'processing'):
        return 'W realizacji';

    elseif ($ordrer_status == 'accept-status'):
        return 'Zaakceptowane';

    elseif ($ordrer_status == 'shipment-status'):
        return 'Dostarczone';

    elseif ($ordrer_status == ''):
        return 'Dostarczone';

    elseif ($ordrer_status == 'completed'):
        return 'Zrealizowane';

    elseif ($ordrer_status == 'cancelled'):
        return 'Odrzucone';
    endif;

}
