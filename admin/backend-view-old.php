<?php
/**
 * Created by PhpStorm.
 * User: mytag
 * Date: 18.08.2021
 * Time: 19:45
 */
?>
<?php $users = get_all_users() ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-9">
            <legend class="file-input__label"><?php _e('Zależności ', 'cc') ?></legend>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?php _e('Imię ', 'cc') ?><?php _e('Nazwisko', 'cc') ?></th>
                    <th scope="col"><?php _e('Powiązani użytkownicy', 'cc') ?></th>
                    <th scope="col"><?php _e('adres MPK', 'cc') ?></th>
                    <th scope="col"><?php _e('Role', 'cc') ?></th>
                    <th scope="col"><?php _e('Login', 'cc') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $key = 1 ?>
                <?php foreach ($users as $user): ?>
                    <?php $role = get_userdata($user->ID)->roles; ?>
                    <?php if (in_array('realizator_new', $role)) : ?>
                        <tr>
                            <th scope="row"><?php echo $key++ ?></th>
                            <td><?php echo $user->display_name ?></td>
                            <td>
                                <?php foreach (array_user_id() as $getID): ?>
                                    <?php if ($user->ID == $getID->user_id): ?>
                                        <?php foreach (get_userdata($getID->subscriber_id) as $subs): ?>
                                            <p><?php echo $subs->display_name ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach (array_user_id() as $getID): ?>
                                    <?php if ($user->ID == $getID->user_id): ?>
                                        <?php if (get_user_meta($getID->subscriber_id, 'mpk', true) != null): ?>
                                            <?php foreach (get_user_meta($getID->subscriber_id, 'mpk', true) as $mpk): ?>
                                                <p> <?php echo $mpk . '<br/>' ?></p>
                                            <?php endforeach; ?>
                                        <?php else: _e('adres nie został ustawiony', 'cc') ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
                            <td> <?php foreach (array_user_id() as $getID): ?>
                                    <?php if ($user->ID == $getID->user_id): ?>
                                        <?php $subscriberRole = $role = get_userdata($getID->subscriber_id)->roles; ?>
                                        <p><?php echo implode(', ', $subscriberRole) . '<br/>' ?></p>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </td>
                            <td>
                                <?php foreach (array_user_id() as $getID): ?>
                                    <?php if ($user->ID == $getID->user_id): ?>
                                        <?php $subscriberRole = $role = get_userdata($getID->subscriber_id)->user_login; ?>
                                        <p><?php echo $subscriberRole . '<br/>' ?></p>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-3 export__section">
            <legend><?php _e('Możliwości','cc')?></legend>
            <form method="post" action="#">
                <div class="form-control">
                    <input type="file">
                </div>
                <div class="buttons d-flex">
                    <button class="btn btn-primary"><?php _e('Pobierz CSV','cc')?></button>
                    <button class="btn btn-secondary"><?php _e('Zapisać','cc')?></button>
                </div>

            </form>

        </div>
    </div>
</div>


