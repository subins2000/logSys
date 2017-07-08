<?php
require 'config.php';
if (isset($_GET['revoke_device']) && $LS->csrf()) {
    if ($LS->revokeDevice($_GET['revoke_device'])) {
        $revoked = true;
    } else {
        $revoked = false;
    }
}

function showMessage($message)
{
    $bg = $message['type'] === 'success' ? 'green' : 'red';

    echo <<<HTML
<div class="card $bg white-text">
    <div class="card-content">
        {$message['message']}
    </div>
</div>
HTML;
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
            <p>The list shows the devices currently authorized to skip 2 Step Login/2FA and login using your account</p>
            <?php
            if (isset($revoked)) {
                if ($revoked) {
                    $message = [
                        'type'    => 'success',
                        'message' => 'Successfully Revoked Device',
                    ];
                } else {
                    $message = [
                        'type'    => 'error',
                        'message' => 'Failed to Revoke Device',
                    ];
                }
            }

            if (isset($message)) {
                showMessage($message);
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
            <h2 id="2FA">2 Factor Auth</h2>
            <?php
            if (isset($_POST['code']) && isset($_POST['secret_key'])) {
                $verification = $LS->TwoFA->verifyCode($_POST['code'], false, $_POST['secret_key']);

                if ($verification) {
                    $LS->TwoFA->enable2FA($_POST['secret_key']);

                    showMessage([
                        'type'    => 'success',
                        'message' => 'Successfully enabled 2FA for your account.',
                    ]);
                } else {
                    showMessage([
                        'type'    => 'error',
                        'message' => 'Incorrect code. Make sure the time on your phone is correct.',
                    ]);
                }
            }

            if (isset($_POST['disable_2fa'])) {
                if ($LS->TwoFA->disable2FA()) {
                    showMessage([
                        'type'    => 'success',
                        'message' => 'Successfully disabled 2FA for your account.',
                    ]);
                } else {
                    showMessage([
                        'type'    => 'error',
                        'message' => 'Could not disable 2FA for your account.',
                    ]);
                }
            }

            $curPageURL = $LS->curPageURL();

            if (!$LS->TwoFA->isActive()) {
                $secretKey  = $LS->TwoFA->generateSecretKey();
                $qrImageURL = $LS->TwoFA->getQRImageURL($secretKey);

                echo <<<HTML
<p>You may use your <b>Google Authenticator</b> app to use in secondary login process (2 Step Auth). This will disable the current 2 Step Login process for your account.</p>

<p>Use <b>Google Authenticator</b> app to scan this QR code.</p>

<center><img src="$qrImageURL" /></center>

<p>Once scanned, type in the verification code you got below :</p>

<form action="$curPageURL" method="POST">
    <input type="hidden" name="secret_key" value="$secretKey" />
    <div class="row">
        <input type="number" class="col l10 m8" name="code" autocomplete="off" />
        <button type="submit" class="col l2 m4 btn red">Verify</button>
    </div>
</form>
HTML;
            } else {
                echo <<<HTML
<p>Your account has 2FA enabled. It is <b>highly recommended</b> that you keep 2FA enabled.</p>
<form action="$curPageURL" method="POST" onsubmit="return confirm('Do you want to disable 2FA ?') ? this.submit() : false;">
    <button name="disable_2fa" class="btn red">Disable 2FA</button>
</form>
HTML;
            }
            ?>
        </div>
    </body>
</html>
