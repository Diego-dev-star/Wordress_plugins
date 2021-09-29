<?php
/**
 * Created by PhpStorm.
 * User: Виталий
 * Date: 22.07.2021
 * Time: 13:52


$order_id  = $order->get_id(); // Get the order ID
$parent_id = $order->get_parent_id(); // Get the parent order ID (for subscriptions…)

$user_id   = $order->get_user_id(); // Get the costumer ID
$user      = $order->get_user(); // Get the WP_User object

$order_status  = $order->get_status(); // Get the order status (see the conditional method has_status() below)
$currency      = $order->get_currency(); // Get the currency used
$payment_method = $order->get_payment_method(); // Get the payment method ID
$payment_title = $order->get_payment_method_title(); // Get the payment method title
$date_created  = $order->get_date_created(); // Get date created (WC_DateTime object)
$date_modified = $order->get_date_modified(); // Get date modified (WC_DateTime object)

$billing_country = $order->get_billing_country(); // Customer billing country


 */

include_once('order-save.php');

function array_user_id()
{
    global $wpdb;
    // $user_id = get_current_user_id();
    $GetSubscribers = $wpdb->get_results("SELECT* FROM wp_subscribers");
    return $GetSubscribers;

}
function add_to_my_list()
{
    global $wpdb;
    $wpdb->insert('wp_subscribers', [
        'user_id' => $_POST['user_id'],
        'subscriber_id' => $_POST['sub-user'],

    ], '%d');
}




