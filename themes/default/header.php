<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<?php if (isset($metaRefresh)): ?>
		<meta http-equiv="refresh" content="<?php echo $metaRefresh['seconds'] ?>; URL=<?php echo $metaRefresh['location'] ?>">
		<?php endif ?>
		<title><?php echo Flux::config('SiteTitle'); if (isset($title)) echo ": $title" ?></title>
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />
		<!--[if IE]>
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux/ie.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />
		<![endif]-->	
		<!--[if lt IE 7]>
		<script src="<?php echo $this->themePath('js/ie7.js') ?>" type="text/javascript"></script>
		<![endif]-->
		<?php if ($session->isLoggedIn()): ?>
		<script type="text/javascript">
			function updatePreferredServer(sel){
				var preferred = sel.options[sel.selectedIndex].value;
				document.preferred_server_form.preferred_server.value = preferred;
				document.preferred_server_form.submit();
			}
		</script>
		<?php endif ?>
		<?php if ($params->get('module') == 'donate'): ?>
		<script type="text/javascript">
			var CashPointRate = <?php echo (float)Flux::config('CashPointRate') ?>;
			function convertCashPointsToDollars(cp, result){
				if (cp.value.length < 1) {
					result.style.visibility = 'hidden';
				}
				
				else {
					var cpoints = parseInt(cp.value);
					var dollars = (cpoints * CashPointRate).toFixed(2);
					var message = '** The total amount for <strong>'+addCommas(cpoints)+' CP</strong> is <strong>$'+addCommas(dollars)+' USD</strong> **';
					result.style.visibility = 'visible';
					result.innerHTML = message;
				}
			}
			function addCommas(nStr)
			{
				nStr += '';
				x     = nStr.split('.');
				x1    = x[0];
				x2    = x.length > 1 ? '.' + x[1] : '';
				
				var rgx = /(\d+)(\d{3})/;
				
				while (rgx.test(x1)) {
					x1 = x1.replace(rgx, '$1' + ',' + '$2');
				}
				
				return x1 + x2;
			}
		</script>
		<?php endif ?>
	</head>
	<body>
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<!-- Header -->
				<td bgcolor="#8ebceb" width="20"></td>
				<td bgcolor="#8ebceb" colspan="3"><a href="<?php echo $this->basePath ?>"><img src="<?php echo $this->themePath('img/logo.gif') ?>" alt="[LOGO]" title="Flux Control Panel" /></a></td>
				<td bgcolor="#8ebceb" width="20"></td>
			</tr>
			<tr>
				<!-- Spacing between header and content -->
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td></td>
				<td width="198">
					<!-- Sidebar -->
					<?php include 'main/sidebar.php' ?>
				</td>
				<!-- Spacing between sidebar and content -->
				<td width="20"></td>
				<td>
					<!-- Login box / User information -->
					<?php include 'main/loginbox.php' ?>
					
					<!-- Content -->
					<table cellspacing="0" cellpadding="0" width="100%" id="content">
						<tr>
							<td width="18"><img src="<?php echo $this->themePath('img/content_tl.gif') ?>" alt="[CONTENT_TOP_LEFT]" /></td>
							<td bgcolor="#f8f8f8"></td>
							<td width="18"><img src="<?php echo $this->themePath('img/content_tr.gif') ?>" alt="[CONTENT_TOP_RIGHT]" /></td>
						</tr>
						
						<tr>
							<td bgcolor="#f8f8f8"></td>
							<td bgcolor="#f8f8f8">