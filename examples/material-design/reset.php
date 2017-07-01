<?php
require 'config.php';
?>
<html>
	<head>
		<?php printHead('Reset Password');?>
	</head>
	<body>
		<?php
        showHeader();
        ?>
		<div class="container">
			<h1>Reset Password</h1>
			<?php
            /**
             * Custom implementation of Reset Password
             */

            if (isset($_GET['resetPassToken'])) {
                if ($LS->verifyResetPasswordToken($_GET['resetPassToken'])) {
                    $msg = array(
                        'color' => 'green',
                        'text'  => <<<HTML

HTML
                    );
                } else {
                    $msg = array(
                        'color' => 'red',
                        'text'  => 'Invalid reset token',
                    );
                }
            } else {

                if (isset($_POST['identification'])) {

                    if (!$LS->userExists($_POST['identification'])) {
                        $msg = array(
                            'color' => 'red',
                            'text'  => 'User does not exist',
                        );
                    } else {
                        $hide_reset_pass_form = true;
                        $uid                  = $LS->login($_POST['identification'], false, false, false);

                        $LS->sendResetPasswordToken(
                            $uid,
                            function ($encodedToken, $url) {
                                return <<<HTML
You requested for resetting your password on logSys Demo. For this, please click the following link :
<blockquote>
	<a href='{$url}?resetPassToken={$encodedToken}'>Reset Password</a>
</blockquote>
Or you may enter this token in the page :
<blockquote>
	{urldecode($encodedToken)}
</blockquote>
HTML;
                            }
                        );

                        $url = Fr\LS::curPageURL();
                        $msg = array(
                            'color' => 'black',
                            'text'  => <<<HTML
An email with instructions has been sent to you. Check your Email Inbox and SPAM folders. You may follow the link in the email or enter the token below :
<form action="$url" method="GET">
	<div class="row">
		<div class="input-field col s12">
			<input type="text" id="token" name="resetPassToken" />
			<label for="token">Token</label>
		</div>
		<div class="row">
			<div class="input-field col s12">
				<button name="submit" class="btn red">Verify</button>
			</div>
		</div>
	</div>
</form>
HTML
                        );
                    }
                }

                if (isset($msg)) {
                    echo <<<HTML
<div class="card-panel {$msg['color']}">
	<span class="white-text">{$msg['text']}</span>
</div>
HTML;
                }

                if (!isset($hide_reset_pass_form)) {
                    ?>
					<form action="<?php echo Fr\LS::curPageURL(); ?>" method="POST">
						<div class="row">
							<div class="input-field col s12">
								<input type="text" id="identification" name="identification" />
								<label for="identification">Username or Email</label>
							</div>
							<div class="row">
								<div class="input-field col s12">
									<button name="submit" class="btn red">Reset Password</button>
								</div>
							</div>
						</div>
					</form>
			<?php
            }
            }
            ?>
		</div>
	</body>
</html>
