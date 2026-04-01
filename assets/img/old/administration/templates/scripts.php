	<!-- JavaScript -->
	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<!-- App JS -->
	<script src="assets/js/app.js"></script>
	
	<?php if($page == 'messages_read'){ ?>
	<script>
		new PerfectScrollbar('.email-navigation');
		new PerfectScrollbar('.email-read-box');
	</script>
	<?php } ?>
	
	<?php if($page == 'messages'){ ?>
	<script>
		new PerfectScrollbar('.email-list');
	</script>
	<?php } ?>