<?php
/**
 * Created by PhpStorm.
 * User: mytag
 * Date: 28.07.2021
 * Time: 13:12
 */

?>

<?php
$cyrrent_user_id = get_current_user_id();
$user_meta = get_userdata($cyrrent_user_id);
$user_roles = $user_meta->roles;
if (in_array('realizator_new', $user_roles)):
    $users = get_users(); ?>
    <div class="user-list">
    <div class="search-usr">
        <input class="jquery-search" type="search" placeholder="<?php _e('Find user','cc')?>">
    </div>
    <form class="sub-form" method="post" action="<?php add_to_my_list()?>" enctype="multipart/form-data">
    <?php foreach ($users as $user): ?>
    <div class="user">
        <div class="avatar">
            <?php echo get_avatar($user->ID)?>
        </div>
        <div class="user-name">
            <?php echo $user->display_name ?>
        </div>
        <div class="add-user">
            <input name="sub-user" type="submit" value="<?php echo $user->ID?>">
            <input name="user_id" type="hidden" value="<?php echo get_current_user_id()?>">
        </div>
    </div>
<?php endforeach; ?>
    </form>
<?php endif; ?>