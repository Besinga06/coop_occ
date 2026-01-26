<li class="dropdown dropdown-user">
	<a class="dropdown-toggle" data-toggle="dropdown">
		<img src="../images/default-avatar.png" alt="">
		<span><?= $_SESSION['fullname']?></span>
		<i class="caret"></i>
	</a>

	<ul class="dropdown-menu dropdown-menu-right" style="min-width: 210px">
		<!-- <li title="Branch1" ><a href="javascript:;"><i class="icon-user"></i> Branch1 </a></li> -->
		<li title="My profile"><a href="profile.php"><i class="icon-user"></i> My profile</a></li>
		<!-- <li title="Database Management" ><a href="update-database.php"><i class="icon-database"></i> Database Management</a></li> -->
		<li title="Logout"><a href="../transaction.php?admin-logout"><i class="icon-switch2"></i> Logout</a></li>
	</ul>
</li>