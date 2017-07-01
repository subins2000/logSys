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
            <p>
                <a href="change.php" class="btn pink" data-ajax>Change Password</a>
                <a href="manage-devices.php" class="btn blue" data-ajax>Manage Devices</a>
            </p>
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
                <a href="profile.php?user=<?php echo $LS->userID; ?>" class="btn green">See Your Profile</a>
            </p>
        </div>
    </body>
</html>
