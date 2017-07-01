<?php
require 'config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<?php printHead('Change Password');?>
	</head>
	<body>
		<?php
        showHeader();
        ?>
		<div class="container">
			<h2>Change Password</h2>
			<?php
            if (isset($_POST['change_password']) && isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['retype_password'])) {

                if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['retype_password'])) {

                    $curpass         = $_POST['current_password'];
                    $new_password    = $_POST['new_password'];
                    $retype_password = $_POST['retype_password'];

                    if ($new_password != $retype_password) {
                        $msg = array(
                            'color' => 'red',
                            'text'  => 'The passwords you entered didn\'t match.',
                        );
                    } else if ($LS->login($LS->getUser('username'), $curpass, false, false) == false) {
                        $msg = array(
                            'color' => 'red',
                            'text'  => 'Current password you entered is wrong !',
                        );
                    } else {
                        $change_password = $LS->changePassword($new_password);
                        if ($change_password === true) {
                            $msg = array(
                                'color' => 'green',
                                'text'  => 'Your password was changed successfully.',
                            );
                        }
                    }
                } else {
                    $msg = array(
                        'color' => 'teal',
                        'text'  => 'Please enter all fields.',
                    );
                }
            }

            if (isset($msg)) {
                echo <<<HTML
			<div class='card-panel {$msg['color']}'>
	<span class='white-text'>{$msg['text']}</span>
</div>
HTML;
            }
            ?>
			<form action="<?php echo $LS->curPageURL(); ?>" method='POST'>
				<div class='row'>
					<div class='input-field col s12'>
						<input id='current_password' name='current_password' type='password' class='validate'>
						<label for='password'>Current Password</label>
					</div>
				</div>
				<div class='row'>
					<div class='input-field col s12'>
						<input id='new_password' name='new_password' type='password' class='validate'>
						<label for='new_password'>New Password</label>
					</div>
				</div>
				<div class='row'>
					<div class='input-field col s12'>
						<input id='retype_password' name='retype_password' type='password' class='validate'>
						<label for='retype_password'>Retype New Password</label>
					</div>
				</div>
				<div class='row'>
					<div class='input-field col s12'>
						<button class='btn green' name='change_password' type='submit'>Change Password</button>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
