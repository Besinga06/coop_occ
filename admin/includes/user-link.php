<li class="dropdown dropdown-user">
	<a class="dropdown-toggle" data-toggle="dropdown">
		<img src="../images/default-avatar.png" alt="">
		<span><?= $_SESSION['fullname'] ?></span>
		<i class="caret"></i>
	</a>

	<ul class="dropdown-menu dropdown-menu-right" style="min-width: 210px">

		<?php if (isset($_SESSION['session_type']) && ($_SESSION['session_type'] == 'member' || $_SESSION['session_type'] == 4)): ?>
			<!-- Member logged in -->
			<li title="My profile">
				<a href="../admin/profile.php"><i class="icon-user"></i> My profile</a>
			</li>
		<?php else: ?>
			<!-- Admin or other user -->
			<li title="My profile">
				<a href="profile.php"><i class="icon-user"></i> My profile</a>
			</li>
		<?php endif; ?>
		<!-- <li title="Database Management" ><a href="update-database.php"><i class="icon-database"></i> Database Management</a></li> -->
		<li title="Logout"><a href="../transaction.php?admin-logout"><i class="icon-switch2"></i> Logout</a></li>
	</ul>
</li>