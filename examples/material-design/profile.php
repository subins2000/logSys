<?php
require 'config.php';

if ( isset( $_GET['user'] ) && is_numeric( $_GET['user'] ) && $LS->userIDExists( $_GET['user'] ) ) {
	$uid = $_GET['user'];
} else if ( $LS->isLoggedIn() ) {
	$uid = $LS->uid;
} else {
	$LS->redirect( 'index.php' );
}

$userInfo = $LS->getUser( '*', $uid );
?>
<!DOCTYPE html>
<html>
	<head>
		<?php printHead( 'Home' );?>
	</head>
	<body>
		<?php
		showHeader();
		?>
		<div class="container">
			<?php
			list( $firstName )     = explode( ' ', $userInfo['name'] );
			list( , $emailDomain ) = explode( '@', $userInfo['email'] );

			echo '<h1><a href="profile.php?user=' . $uid . '" data-ajax>' . $firstName . '</a></h1>';
			?>
			<div>
				Username : <div class='chip'>@<?php echo $userInfo['username']; ?></div>
			</div>
			<div>
				Full Name : <div class='chip'><?php echo $userInfo['name']; ?></div>
			</div>
			<div>
				Email domain : <div class='chip'><?php echo $emailDomain; ?></div>
			</div>
			<div>
				Member for <?php echo $LS->joinedSince( $uid ); ?>.
			</div>
		</div>
	</body>
</html>
