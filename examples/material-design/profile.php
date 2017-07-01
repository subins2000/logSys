<?php
require 'config.php';

if (isset($_GET['user']) && is_numeric($_GET['user']) && $LS->userIDExists($_GET['user'])) {
    $uid = $_GET['user'];
} else if ($LS->isLoggedIn()) {
    $uid = $LS->getUser('id');
} else {
    $LS->redirect('index.php');
}

$userInfo = $LS->getUser('*', $uid);
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
            <?php
            list($firstName)     = explode(' ', $userInfo['name']);
            list(, $emailDomain) = explode('@', $userInfo['email']);

            echo '<h1><a href="profile.php?user=' . $uid . '" data-ajax>' . $firstName . '</a></h1>';

            if ($LS->isLoggedIn() && $uid === $LS->getUser('id')) {
                echo '<p><i>This is you.</i></p>';
            }
            ?>
            <table class="striped">
                <tbody>
                    <tr>
                        <td>Username</td>
                        <td>@<?php echo $userInfo['username']; ?></td>
                    </tr>
                    <tr>
                        <td>Full Name</td>
                        <td><?php echo $userInfo['name']; ?></td>
                    </tr>
                    <tr>
                        <td>Email Domain</td>
                        <td><?php echo $emailDomain; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center">Member For                                                                                                                                                                                                  <?php echo $LS->joinedSince($uid); ?>.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
