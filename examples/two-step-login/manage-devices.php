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
<html>
    <head>
        <title>Log In With Two Step Verification</title>
    </head>
    <body>
        <div class="content">
            <h2>Two Step Log In</h2>
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
                echo "<table border='1' cellpadding='10px'>
                <thead>
                    <th>Session ID</th>
                    <th>Last Accessed</th>
                    <th></th>
                </thead>
                <tbody>";
                foreach ($devices as $device) {
                    echo "<tr>
                        <td>{$device['token']}</td>
                        <td>{$device['last_access']}</td>
                        <td><a href='?revoke_device={$device['token']}" . $LS->csrf('g') . "'>Revoke Access</a></td>
                    </tr>";
                }
                echo '</tbody></table>';
            }
            ?>
        </div>
    </body>
</html>
