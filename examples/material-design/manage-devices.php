<?php
require 'config.php';
if (isset($_GET['revoke_device']) && $LS->csrf()) {
    if ($LS->revokeDevice($_GET['revoke_device'])) {
        $revoked = true;
    } else {
        $revoked = false;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php printHead('Manage Devices');?>
    </head>
    <body>
        <?php
        showHeader();
        ?>
        <div class="container">
            <h2>Manage Devices</h2>
            <p>The list shows the devices currently authorized to login using your account</p>
            <?php
            if (isset($revoked)) {
                if ($revoked) {
                    echo '<h2>Successfully Revoked Device</h2>';
                } else {
                    echo '<h2>Failed to Revoke Device</h2>';
                }
            }
            $devices = $LS->getDevices();
            if (count($devices) == 0) {
                echo '<p>No devices are authorized to use your account by skipping 2 Step Verification.</p>';
            } else {
                echo <<<HTML
<table>
    <thead>
        <th>Device ID</th>
        <th>Last Accessed</th>
        <th></th>
    </thead>
    <tbody>
HTML;
                foreach ($devices as $device) {
                    $lastAccess = date('Y-m-d H:i:s e', $device['last_access']);

                    echo <<<HTML
<tr>
    <td>{$device['token']}</td>
    <td>$lastAccess</td>
    <td><a href="?revoke_device={$device['token']}{$LS->csrf('g')}" class="btn red">Revoke Access</a></td>
</tr>
HTML;
                }
                echo <<<HTML
    </tbody>
</table>
HTML;
            }
            ?>
        </div>
    </body>
</html>
