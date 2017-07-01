<nav class="nav-extended row">
	<div class="nav-wrapper col l12">
		<a href="index.php" class="brand-logo left" data-ajax>My Site</a>
		<ul id="nav-mobile" class="right">
			<?php
			if($LS->isLoggedIn()){
			?>
				<li>
					<a href="home.php" class="dropdown-button" href="#!" data-activates="dropdown1">
						<?php echo $LS->getUser("name");?>
					</a>
				</li>
			<?php
			}else{
			?>
				<li><a href="login.php" class="btn green" data-ajax>Sign In</a></li>
				<li><a href="register.php" class="btn red" data-ajax>Sign Up</a></li>
			<?php
			}
			?>
		</ul>
	</div>
</nav>
<ul id="dropdown1" class="dropdown-content">
	<li><a href="home.php" data-ajax>Home</a></li>
	<li><a href="profile.php?user=<?php echo $LS->userID;?>">Profile</a></li>
	<li class="divider"></li>
	<li><a href="logout.php">Sign Out</a></li>
</ul>
