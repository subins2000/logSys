<?php
require 'config.php';
?>
<html>
    <head>
        <title>Log In With Two Step Verification</title>
    </head>
    <body>
        <div class="content">
            <h2>Two Step Log In</h2>
            <p>This demo shows how logSys can be used to implement two step verification.</p>
            <?php
            $two_step_login_form_display = false;
            try {
                if (isset($_POST['action_login'])) {
                    /**
                     * Try login
                     */
                    $LS->twoStepLogin($_POST['login'], $_POST['password'], isset($_POST['remember_me']));
                } else {
                    /**
                     * Handle 2 Step Login
                     */
                    $LS->twoStepLogin();
                }
            } catch (Fr\LS\TwoStepLogin $TSL) {
                if ($TSL->getStatus() === 'login_fail') {
                    echo '<h2>Error</h2><p>Username / Password Wrong !</p>';
                } elseif ($TSL->getStatus() === 'blocked') {
                    $blockInfo = $TSL->getBlockInfo();
                    echo '<h2>Error</h2><p>Too many login attempts. You can attempt login after ' . $blockInfo['minutes'] . ' minutes (' . $blockInfo['seconds'] . ' seconds)</p>';
                } elseif ($TSL->getStatus() === 'enter_token_form' || $TSL->getStatus() === 'invalid_token') {
                    $two_step_login_form_display = true;

                    if ($TSL->getStatus() === 'invalid_token') {
                        echo '<p>Wrong token. You have ' . $TSL->getOption('tries_left') . ' tries left</p>';
                    } ?>
                    <form action='<?php echo Fr\LS::curPageURL(); ?>' method='POST'>
                        <p>A token was sent to your E-Mail address. Paste the token in the box below :</p>
                        <label>
                            <input type='text' name='two_step_login_token' placeholder='Paste the token here... (case sensitive)' />
                        </label><br/><br/>
                        <label>
                            <span>Remember this device ?</span>
                            <input type='checkbox' name='two_step_login_remember_device' />
                        </label><br/><br/>
                        <input type='hidden' name='two_step_login_uid' value='<?php echo $TSL->getOption('uid'); ?>' />
                        <?php
                        if ($TSL->getOption('remember_me')) {
                            ?>
                            <input type='hidden' name='two_step_login_remember_me' />
                        <?php

                        }
                    echo $LS->csrf('i'); ?>
                        <label>
                            <button>Verify</button>
                            <a onclick="window.location.reload();" href="#">Resend Token</a>
                        </label>
                    </form>
                <?php

                } elseif ($TSL->getStatus() === 'login_success') {
                    // Nothing to do. Auto Init will do the redirect if it's enabled
                } elseif ($TSL->isError()) {
                    echo '<h2>Error</h2><p>' . $TSL->getStatus() . '</p>';
                }
            }
                if (! $two_step_login_form_display) {
                    ?>
                <form action="login.php" method="POST" style="margin:0px auto;display:table;">
                    <label>
                        <p>Username / E-Mail</p>
                        <input name="login" type="text"/>
                    </label><br/>
                    <label>
                        <p>Password</p>
                        <input name="password" type="password"/>
                    </label><br/>
                    <label>
                        <p>
                            <input type="checkbox" name="remember_me"/> Remember Me
                        </p>
                    </label>
                    <div clear></div>
                    <button style="width:150px;" name="action_login">Log In</button>
                </form>
            <?php

                }
            ?>
            <style>
                input[type=text], input[type=password]{
                    width: 230px;
                }
            </style>
            <p>
                <p>Don't have an account ?</p>
                <a class="button" href="register.php">Register</a>
            </p>
            <p>
                <p>Forgot Your Password ?</p>
                <a class="button" href="reset.php">Reset Password</a>
            </p>
        </div>
    </body>
</html>
