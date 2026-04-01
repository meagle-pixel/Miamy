<?php include('templates/messages/layout_start.php'); ?>
<div class="">
	<div class="email-list">
		<?php if(is_array($listMessages)){ ?>
			<?php foreach($listMessages as $message) { 
				
				$expordest = $message['expediteur'];
				
				if(isset($_GET['list']) && $_GET['list'] == 'sent')
					$expordest = $message['destinataire'];
				
				$utilisateur = getUser($expordest);
			
			?>
			<a href="index.php?mod=messages_read&id=<?php echo $message['id']; ?>">
				<div class="d-md-flex align-items-center email-message px-3 py-1">
					<div class="d-flex align-items-center email-actions">
						<input class="form-check-input" type="checkbox" value="" /> &nbsp;
						<p class="mb-0"><b><?php echo $utilisateur['userinfo']['prenom']; ?> <?php echo $utilisateur['userinfo']['nom']; ?></b>
						</p>
					</div>
					<div class="">
						<p class="mb-0"><?php echo substr($message['message'],0,100); ?></p>
					</div>
					<div class="ms-auto">
						<p class="mb-0 email-time"><?php echo dateTimeFrFromTimestamp($message['date']); ?> (<?php echo get_friendly_time_ago($message['date']); ?>)</p>
					</div>
				</div>
			</a>
			<?php } ?>
		<?php }else { ?>
			<a href="#">
				<div class="d-md-flex align-items-center email-message px-3 py-1">
					Aucun Message
				</div>
			</a>
		<?php } ?>
	</div>
</div>
<?php include('templates/messages/layout_end.php'); ?>