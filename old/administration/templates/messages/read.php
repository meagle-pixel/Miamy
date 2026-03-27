<?php include('templates/messages/layout_start.php'); ?>

<div class="email-read-box p-3 ps ps--active-y">
	<div class="d-flex align-items-center">
		<img src="users/<?php echo $utilisateur['picture']; ?>" width="42" height="42" class="rounded-circle" alt="">
		<div class="flex-grow-1 ms-2">
			<p class="mb-0 font-weight-bold"><?php echo $utilisateur['userinfo']['prenom']; ?> <?php echo $utilisateur['userinfo']['nom']; ?></p>
			<div class="dropdown">
				<div>à moi</div>
			</div>
		</div>
		
		<p class="mb-0 chat-time ps-5 ms-auto"><?php echo dateTimeFrFromTimestamp($current_message['date']); ?> (<?php echo get_friendly_time_ago($current_message['date']); ?>)</p> 
	</div>
	<hr/>
	<div class="email-read-content px-md-5 py-5">
		<p><?php echo preg_replace("/\\n/m", "</p><p>", htmlspecialchars($current_message['message'])); ?></p>
	</div>
	<div class="ps__rail-y" style="top: 0px; height: 530px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 465px;"></div></div>
</div>

<?php include('templates/messages/layout_end.php'); ?>