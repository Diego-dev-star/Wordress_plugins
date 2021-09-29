<?php $user_id = get_current_user_id();


?>
<table class="table">
    <thead>
    <tr>
        <th scope="col"><?php _e('Zamówienie', 'cc') ?></th>
        <th scope="col"><?php _e('Status', 'cc') ?></th>
        <th scope="col"><?php _e('Data złożenia', 'cc') ?></th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (array_user_id() as $subscriber):
        $args = array(
            'customer_id' => $subscriber->subscriber_id
        );
        $order = wc_get_orders($args);
        //echo var_dump($order);
        ?>
        <?php if ($user_id == $subscriber->user_id): ?>
        <?php $subscriberArray = get_userdata($subscriber->subscriber_id); ?>

        <?php if ($order != null): ?>
            <?php foreach ($order as $order_user): ?>
                <tr>
                    <td>
                        <span> <?php echo '#' . $order_user->get_id() ?></span> <?php echo $subscriberArray->display_name; ?>
                    </td>
                    <td> <span
                            <?php if ($order_user->get_status() == 'waiting'):
                                echo 'class="yelow"';
                            elseif ($order_user->get_status() == 'processing'):
                                echo 'class="green"';
                            elseif ($order_user->get_status() == 'cancelled'):
                                echo 'class="red"';
                            endif;
                            ?>


                        >
                        <?php echo translate_order_status($order_user->get_status()) ?></span></td>
                    <td><?php echo date_format($order_user->get_date_created(), 'd.m.y') ?></td>
                    <td>
                        <a href="<?php echo esc_url(add_query_arg('order', $order_user->get_id())); ?>"
                           target="_blank"
                           class="button"><?php _e('Zobacz szczegóły', 'cc') ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td><?php echo $subscriberArray->display_name; ?></td>
                <td><span class="no-orders"><?php _e('Brak zamówień', 'cc') ?></span></td>
                <td> -</td>
                <td>
                    <button id="u_id" class="btn" disabled><?php _e('Zobacz szczegóły', 'cc') ?></button>
                </td>

            </tr>
        <?php endif; ?>

    <?php endif; ?>
    <?php endforeach; ?>

    </tbody>
</table>

