<!--start email wrapper-->
<div class="email-wrapper">
	<div class="email-sidebar">
		<div class="email-sidebar-header d-grid"> <a href="javascript:;" class="btn btn-primary compose-mail-btn"><i class='bx bx-plus me-2'></i> Nouveau</a>
		</div>
		<div class="email-sidebar-content">
			<div class="email-navigation">
				<div class="list-group list-group-flush"> 
					<a href="index.php?mod=messages" class="list-group-item <?php if(!isset($_GET['list'])){ echo  'active'; } ?> d-flex align-items-center"><i class='bx bxs-inbox me-3 font-20'></i><span>Boite de reception</span><?php if($nbmessages > 0){ ?><span class="badge bg-primary rounded-pill ms-auto"><?php echo $nbmessages; ?></span><?php } ?></a>
					<a href="index.php?mod=messages&list=unread" class="list-group-item <?php if(isset($_GET['list']) && $_GET['list'] == 'unread'){ echo  'active'; } ?> d-flex align-items-center"><i class='bx bxs-inbox me-3 font-20'></i><span>Non lus</span><?php if($nbmessagesunread > 0){ ?><span class="badge bg-primary rounded-pill ms-auto"><?php echo $nbmessagesunread; ?></span><?php } ?></a>
					<a href="index.php?mod=messages&list=send" class="list-group-item <?php if(isset($_GET['list']) && $_GET['list'] == 'send'){ echo  'active'; } ?> d-flex align-items-center"><i class='bx bxs-send me-3 font-20'></i><span>Envoyés</span><?php if($nbmessagessent > 0){ ?><span class="badge bg-primary rounded-pill ms-auto"><?php echo $nbmessagessent; ?></span><?php } ?></a>
					<a href="index.php?mod=messages&list=trash" class="list-group-item <?php if(isset($_GET['list']) && $_GET['list'] == 'trash'){ echo  'active'; } ?> d-flex align-items-center"><i class='bx bxs-trash-alt me-3 font-20'></i><span>Corbeille</span><?php if($nbmessagesdelete > 0){ ?><span class="badge bg-primary rounded-pill ms-auto"><?php echo $nbmessagesdelete; ?></span><?php } ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="email-header d-xl-flex align-items-center">
		<div class="d-flex align-items-center">
			<div class="email-toggle-btn"><i class='bx bx-menu'></i>
			</div>
			<div class="btn btn-white">
				<input class="form-check-input" type="checkbox">
			</div>
			<div class="">
				<button type="button" class="btn btn-white ms-2"><i class='bx bx-downvote me-0'></i>
				</button>
			</div>
			<div class="">
				<button type="button" class="btn btn-white ms-2"><i class='bx bx-trash me-0'></i>
				</button>
			</div>
		</div>
		<div class="flex-grow-1 mx-xl-2 my-2 my-xl-0">
			<div class="input-group">	<span class="input-group-text bg-transparent"><i class="bx bx-search"></i></span>
				<input type="text" class="form-control" placeholder="Recherche message">
			</div>
		</div>
		<div class="ms-auto d-flex align-items-center">
			<button class="btn btn-white px-2 ms-2"><i class='bx bx-chevron-left me-0'></i>
			</button>
			<button class="btn btn-white px-2 ms-2"><i class='bx bx-chevron-right me-0'></i>
			</button>
		</div>
	</div>
	<div class="email-content">