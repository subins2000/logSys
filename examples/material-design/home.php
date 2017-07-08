<?php
require 'config.php';
if (isset($_POST['newName'])) {
    $_POST['newName'] = $_POST['newName'] == '' ? 'Dude' : $_POST['newName'];
    $LS->updateUser(array(
        'name' => $_POST['newName'],
    ));
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php printHead('Home');?>
    </head>
    <body>
        <?php
        showHeader();
        ?>
        <div class="container">
            <h1>Welcome</h1>
            <p>You have been successfully logged in. You registered on this website <strong><?php echo $LS->joinedSince(); ?></strong> ago.</p>
            <p>
                Here is the full data that the database stores about this user :
            </p>
            <pre><?php
                 $details = $LS->getUser();
                 print_r($details);
                 ?></pre>
            <h2>Security</h2>
            <p>Manage security of your account</p>
            <div class="row">
                <div class="col l4 center">
                    <a href="change-password.php" class="btn pink" data-ajax>Change Password</a>
                </div>
                <div class="col l4 center">
                    <a href="manage-devices.php" class="btn blue" data-ajax>Manage Devices</a>
                </div>
                <div class="col l4 center">
                    <a href="manage-devices.php#2FA" class="btn red" data-ajax>2 Factor Auth</a>
                </div>
            </div>
            <h2>Profile</h2>
            <p>
                Change the name of your account :
            </p>
            <form action="home.php" method="POST">
                <div class="input-field row">
                    <input name="newName" placeholder="New name" class="col l10" />
                    <button class="btn red col l2">Change Name</button>
                </div>
            </form>
            <p>
                <a href="profile.php?user=<?php echo $LS->getUserID(); ?>" class="btn green">See Your Profile</a>
            </p>
        </div>
    </body>
</html>
