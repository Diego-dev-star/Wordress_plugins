<?php
global $woocommerce;
$order_id = $_GET['order'];
$order_info = new WC_Order($order_id);


?>
<h2 class="title"><?php _e('Zamówienie #', 'cc') ?><?php echo $order_id ?></h2>
<form action="<?php saving_change_order() ?>" method="post">
    <div class="container">
        <div class="row flex">
            <div class="order-setting custom-col-6">
                <h4><?php _e('Ustawienie zamówienia', 'cc') ?></h4>
                <div class="date">
                    <span class="div-title"><?php _e('Data utworzenia:', 'cc') ?></span>
                    <div class="date-input">
                        <input type="date" name="order_date"
                               value="<?php echo date_format($order_info->get_date_modified(), 'Y-m-d') ?>"
                               pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
                        <div class="time">
                            <span>/</span>
                            <input type="number" max="23" name="order_date_hours"
                                   value="<?php echo date_format($order_info->get_date_modified(), 'H') ?>"
                                   pattern="([01]?[0-9]{1}|2[0-3]{1})">
                            <span>:</span>
                            <input type="number" max="59" name="order_date_min"
                                   value="<?php echo date_format($order_info->get_date_modified(), 'i') ?>"
                                   pattern="[0-5]{1}[0-9]{1}">
                            <input type="hidden" name="order_date_sec"
                                   value="<?php echo date_format($order_info->get_date_modified(), 's') ?>">
                        </div>
                    </div>
                    <div class="status">
                        <div class="select">
                            <div class="marg"><?php _e('Status zamówienia', 'cc') ?></div>
                            <?php $statuses = get_order_value_status();
                            $check = custom_selected($order_id); ?>
                            <select name="order-status">
                                <?php foreach ($statuses as $key => $status): ?>
                                    <option value="<?php echo $key ?>"
                                        <?php if ($check->scalar == $key): echo "selected";endif; ?>
                                    ><?php echo $status ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="button"><?php _e('Zapisz', 'cc') ?></button>


                        </div>
                    </div>

                </div>
            </div>
            <div class="subscriber-info custom-col-6">
                <h4 id="correct"><?php _e('Dane zamawiającego', 'cc') ?> <span class="correct"><i
                                class="correct-btn"></i></span></h4>
                <div class="info">
                    <div class="info-item"><span class="info-title"><?php _e('IP: ', 'cc') ?></span>
                        <span><?php echo $order_info->get_customer_ip_address() ?></span>
                    </div>
                    <div class="info-item"><span class="info-title"><?php _e('Imię Nazwisko: ', 'cc') ?></span>
                        <span><?php echo $order_info->get_billing_first_name() ?><?php echo $order_info->get_billing_last_name() ?></span>
                    </div>
                    <div class="info-item"><span class="info-title"><?php _e('Mail: ', 'cc') ?></span>
                        <span><?php echo $order_info->get_billing_email() ?>
                            </span>
                    </div>
                    <div class="info-item"><span class="info-title"><?php _e('Telefon: ', 'cc') ?></span>
                        <span><?php echo $order_info->get_billing_phone() ?></span>
                    </div>
                </div>

            </div>
        </div>

</form>
<table class="table product">
    <thead>
    <tr>
        <th scope="col"><?php _e('Product', 'cc') ?></th>
        <th><?php _e(' ', 'cc') ?></th>
        <th scope="col"><?php _e('Cena Netto', 'cc') ?></th>
        <th scope="col"><?php _e('Ilość', 'cc') ?></th>
        <th scope="col"><?php _e('Wartość netto', 'cc') ?></th>
    </tr>
    </thead>
    <?php foreach ($order_info->get_items() as $product): ?>


        <?php $data = $product->get_data() ?>
        <tr>
            <td>
                <?php $product_id = get_page_by_title($product->get_name(), OBJECT, 'product')->ID ?>
                <?php echo get_the_post_thumbnail($product_id, 'thumbnail') ?>

            </td>
            <td>
                <?php echo $product->get_name() ?>
            </td>
            <td>
                <?php echo ($data['total'] / $product->get_quantity()) . get_woocommerce_currency_symbol() ?>
            </td>
            <td>
                <?php echo $product->get_quantity() ?>
            </td>
            <td>
                <?php echo $data['total'] . get_woocommerce_currency_symbol() ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td class="delivery">
            <img src="<?php echo plugins_url('client-cart/assets/img/del.svg') ?>">

        </td>
        <td>
            <?php echo $order_info->get_shipping_method() ?>
        </td>
        <td>
        </td>
        <td>
        </td>
        <td>
            <?php if ($order_info->get_shipping_method() == 'Darmowa wysyłka'):
                echo '0' . get_woocommerce_currency_symbol();
            endif; ?>
        </td>
    </tr>

</table>


<?php
//это  подстраница
/*1) ПОЛУЧАЮ ПО ID  ИЗ GET  ORDER_ID
    2) ВЫВОЖУ ДЕТАЛИ
        3) ДЕЛАЮ ВОЗМОЖНОСТЬ РЕДАКТИРОВАНИЯ
            4) ЗАПИСЬ В БД (СКОРЕЕ ВСЕГО)
*/
?>


