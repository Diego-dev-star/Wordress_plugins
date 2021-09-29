<?php
function saving_change_order(){
    global $wpdb;
    $status = $_POST['order-status'];
    $order_id =$_GET['order'];
    $wpdb->update($wpdb->prefix.'posts',
        [
            'post_status'=> $status,

        ],
    [
        'ID' => $order_id
    ]

    );

}

function custom_selected($order_id){
    global $wpdb;
    $check = $wpdb->get_results("SELECT* FROM wp_posts WHERE ID=$order_id");
    return (object)$check[0]->post_status;
}