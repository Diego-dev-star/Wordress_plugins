<?php
/**
 * Created by PhpStorm.
 * User: mytag
 * Date: 18.08.2021
 * Time: 19:45
 */
global $wpdb;

?>
<?php $data = $wpdb->get_results("SELECT user_id , subscriber_id FROM wp_subscribers GROUP BY user_id HAVING COUNT(*) >0 ");

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-9">
            <legend class="file-input__label"><?php _e('Zależności ', 'cc') ?></legend>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?php _e('Imię i nazwisko', 'cc') ?></th>
                    <th scope="col"><?php _e('Email', 'cc') ?></th>
                    <th scope="col"><?php _e('MPK ID', 'cc') ?></th>
                    <th scope="col"><?php _e('Ile połączeń', 'cc') ?></th>
                    <th scope="col"><?php _e('Zobacz informacje', 'cc') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $key = 1 ?>
                <?php
                foreach ($data

                as $line): ?>
                <?php $user_info = get_userdata($line->user_id);
                $count = $wpdb->get_var("SELECT COUNT(*) FROM wp_subscribers WHERE user_id = $line->user_id ")
                ?>
                <tr>
                    <th scope="row"><?php echo $key++ ?></th>
                    <td><?php echo $user_info->display_name ?></td>
                    <td><?php echo $user_info->user_email ?></td>
                    <?php $mpk = get_user_meta($line->user_id, 'mpk', true); ?>
                    <?php if (is_array($mpk)): ?>
                        <td><?php echo implode(', ', get_user_meta($line->user_id, 'mpk', true)) ?></td>
                    <?php elseif ($mpk): ?>
                        <td><?php echo get_user_meta($line->user_id, 'mpk', true) ?></td>
                    <?php elseif ($mpk == null): ?>
                        <td><?php _e('pusta wartość', 'cc') ?></td>
                    <?php endif; ?>
                    <td><?php echo $count ?></td>
                    <td>
                        <div class="user__subs">
                            <button class="btn btn-primary" data-toggle="modal"
                                    data-target="#info_<?php echo $line->user_id ?>">
                                <?php _e('informacje o użytkownikach', 'cc') ?>
                            </button>
                        </div>
                    </td>
                    <div class="modal fade sub__modal" id="info_<?php echo $line->user_id ?>" tabindex="-1" role="dialog"
                         aria-labelledby="info_<?php echo $line->user_id ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"
                                        id="exampleModalLongTitle"><?php _e('Informacje o użytkownikach', 'cc') ?>
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <?php $allMySubs = $wpdb->get_results("SELECT subscriber_id FROM wp_subscribers WHERE user_id = $line->user_id"); ?>
                                    <div class="row">
                                        <div class="col-4">
                                            <strong><?php _e('Imię i nazwisko','cc')?></strong>
                                        </div>
                                        <div class="col-4">
                                            <strong><?php _e('Email','cc')?></strong>
                                        </div>
                                        <div class="col-4">
                                            <strong><?php _e('Rola','cc')?></strong>
                                        </div>
                                    </div>
                                    <?php foreach ($allMySubs as $line): ?>
                                        <?php $subscriberInfo = get_userdata($line->subscriber_id);
                                        $role = get_userdata($line->subscriber_id)->roles;
                                        ?>

                                        <div class="sub__modal_item">
                                            <div class="row">
                                                <div class="col-4">
                                                    <?php echo $subscriberInfo->display_name; ?>
                                                </div>
                                                <div class="col-4">
                                                    <span><?php echo $subscriberInfo->user_email; ?></span>
                                                </div>
                                                <div class="col-4">
                                                    <?php if ($role != null): ?>
                                                        <span><?php echo implode(' ', $role); ?></span>
                                                    <?php else: ?>
                                                        <span><?php _e('Nie zainstalowano podczas importu', 'cc') ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>

                </tbody>
            </table>
            <div class="user__infobuton">
                <button class="btn btn-primary" data-toggle="modal"
                        data-target="#info"><?php _e('informacje o użytkownikach', 'cc') ?></button>
            </div>
            <div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="info" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="exampleModalLongTitle"><?php _e('Informacje o użytkownikach', 'cc') ?>I</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning"
                                 role="alert"><?php _e('dla nowych użytkowników hasło to 1234567890', 'cc') ?></div>
                            <?php foreach (get_all_users() as $user): ?>
                                <p>Imię: <?php echo $user->display_name ?> | ID: <?php echo $user->ID ?></p>
                            <?php endforeach; ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3 export__section">
            <legend><?php _e('Możliwości', 'cc') ?></legend>
            <form enctype="multipart/form-data" method="post" action="<?php insert_to_database() ?>">
                <?php wp_nonce_field('import', 'fileup_nonce'); ?>
                <div class="file-drop-area">
                    <span class="fake-btn"><?php _e('Wybierz plik', 'cc') ?></span>
                    <span class="file-msg"><?php _e('lub przeciągnij i upuść pliki tutaj', 'cc') ?></span>
                    <input class="file-input" name="import" type="file" accept=".csv" multiple>
                </div>
                <div class="buttons d-flex">
                    <button class="btn btn-secondary"><?php _e('Zapisać', 'cc') ?></button>
                </div>
            </form>
            <div class="buttons d-flex">
                <a class="btn btn-primary w-100" href="<?php echo plugins_url('/client-cart/admin/csv/data.csv') ?>"
                   download=""><?php _e('Pobierz CSV', 'cc') ?></a>
            </div>
            <div class="messages">

            </div>
        </div>
    </div>
</div>