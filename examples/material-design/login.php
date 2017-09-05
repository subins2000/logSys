<?php
require 'config.php';

$two_step_login_active = false;

try {
    if (isset($_POST['login']) && isset($_POST['password'])) {
        $identification = $_POST['login'];
        $password       = $_POST['password'];

        if (empty($identification) || empty($password)) {
            $msg = array(
                'color' => 'teal',
                'text'  => 'Please enter username and password',
            );
        } else {
            $login = $LS->twoStepLogin($identification, $password, isset($_POST['remember_me']));
        }
    } else {
        $LS->twoStepLogin();
    }
} catch (Fr\LS\TwoStepLogin $TSL) {
    if ($TSL->getStatus() === 'login_fail') {
        $msg = array(
            'color' => 'red',
            'text'  => 'Username / Password Wrong !',
        );
    } else if ($TSL->getStatus() === 'blocked') {

        $blockInfo = $TSL->getBlockInfo();

        $msg = array(
            'color' => 'red',
            'text'  => 'Too many login/token attempts. You can attempt login after ' . $blockInfo['minutes'] . ' minutes (' . $blockInfo['seconds'] . ' seconds)',
        );

    } else if ($TSL->getStatus() === 'enter_token_form' || $TSL->getStatus() === 'invalid_token') {
        $two_step_login_enter_token_form = true;
        $remember_me                     = $TSL->getOption('remember_me');

        if ($TSL->getStatus() === 'invalid_token') {
            $msg = array(
                'color' => 'red',
                'Wrong token. You have ' . $TSL->getOption('tries_left') . ' tries left',
            );
        }

    } else if ($TSL->getStatus() === 'login_success') {
        // Nothing to do. Auto Init will do the redirect if it's enabled
    } else if ($TSL->isError()) {
        echo '<h2>Error</h2><p>' . $TSL->getStatus() . '</p>';
    }
}

if (isset($_POST['ajax'])) {

}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php printHead('Sign In');?>
	</head>
	<body>
		<?php
        showHeader();
        ?>
		<div class='container'>
			<h1>Sign In</h1>
			<?php
            if (isset($msg)) {
                echo <<<HTML
			<div class='card-panel {$msg['color']}'>
	<span class='white-text'>{$msg['text']}</span>
</div>
HTML;
            }
            if (isset($two_step_login_enter_token_form)) {
                ?>
				<form action='<?php echo Fr\LS::curPageURL(); ?>' method='POST'>
					<p>A token was sent to your E-Mail address. Paste the token in the box below :</p>
					<label>
						<input type='text' name='two_step_login_token' placeholder='Paste the token here... (case sensitive)' />
					</label>
					<div class='row'>
						<div class='input-field col s12'>
							<input type='checkbox' name='two_step_login_remember_device' id='two_step_login_remember_device' />
							<label for='two_step_login_remember_device'>Remember this device ?</label>
						</div>
					</div>
					<input type='hidden' name='two_step_login_uid' value='<?php echo $TSL->getOption('uid'); ?>' />
					<?php
                    if ($remember_me) {
                            ?>
						<input type='hidden' name='two_step_login_remember_me' />
					<?php
                    }
                        echo $LS->csrf('i');
                        ?>
					<div class='row'>
						<div class='input-field col s12'>
							<button class='btn green'>Verify</button>
							<a onclick='window.location.reload();' href='#' class='btn'>Resend Token</a>
						</div>
					</div>
				</form>
			<?php
            } else {
                ?>
				<form action='login.php' method='POST'>
					<div class='row'>
						<div class='input-field col s12'>
							<input id='email' name='login' type='text' class='validate'>
							<label for='email'>Username or Email</label>
						</div>
					</div>
					<div class='row'>
						<div class='input-field col s12'>
							<input id='password' name='password' type='password' class='validate'>
							<label for='password'>Password</label>
						</div>
					</div>
					<div>
						<input type='checkbox' checked='checked' id='remember_me' name='remember_me' />
						<label for='remember_me'>Remember Me</label>
					</div>
					<div class='row'>
						<div class='input-field col s12'>
							<button class='btn green' name='action_login'>Sign In</button>
						</div>
					</div>
				</form>
				<p>
					Don't have an account ? <a class='btn red' href='register.php' data-ajax>Register</a>
				</p>
				<p>
					Forgot Your Password ? <a class='btn pink' href='reset.php' data-ajax>Reset Password</a>
				</p>
			<?php
            }
            ?>
		</div>
	</body>
</html>
