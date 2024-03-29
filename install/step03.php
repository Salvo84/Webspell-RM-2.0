<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/
$fatal_error = false;
if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    $php_version_check = '<span class="label label-danger">'.$_language->module['no'].'</span>';
    $fatal_error = true;
} else {
    $php_version_check = '<span class="label label-success">'.$_language->module['yes'].'</span>';
}

if (function_exists('mysqli_connect')) {
    $sql_check = '<span class="label label-success">'.$_language->module['available'].'</span>';
} else {
    $sql_check = '<span class="label label-danger">'.$_language->module['unavailable'].'</span>';
    $fatal_error = true;
}

if (function_exists('mb_substr')) {
    $mb_check = '<span class="label label-success">'.$_language->module['available'].'</span>';
} else {
    $mb_check = '<span class="label label-danger">'.$_language->module['unavailable'].'</span>';
    $fatal_error = true;
}

?>
<div class="row marketing">
    <div class="col-xs-12">
		<div class="card">
            <div class="card-head">
                <h3 class="card-title"><?=$_language->module['set_chmod']; ?></h3>
			</div>
			<div class="panel-body">

			<div class="card col-xs-6">
			<div class="col-xs-4"><?=$_language->module['php_version']; ?> &gt;= 5.5
			</div>
			<div class="col-xs-2"><?=$php_version_check; ?>
			</div>
			</div>

			<div class="card col-xs-6">
			<div class="col-xs-4"><?=$_language->module['multibyte_support']; ?>
			</div>
			<div class="col-xs-2"><?=$mb_check; ?>
			</div>
			</div>

			<div class="card col-xs-6">
			<div class="col-xs-4"><?=$_language->module['sql_support']; ?>
			</div>
			<div class="col-xs-2"><?=$sql_check; ?>
			</div>
			</div>

			<div class="card col-xs-6">
			<div class="col-xs-4">sql.php
			</div>
			<div class="col-xs-2"><?php
		if (@file_exists('../system/sql.php') && @is_writable('../system/sql.php')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else if (is_writable('..')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else {
			echo '<span class="label label-danger">' . $_language->module['unwriteable'] . '</span><br>
		<div class="alert alert-danger">' . $_language->module['sql_error'] . '</div>';
		} ?>
			</div>
			</div>

			<div class="card col-xs-6">
			<div class="col-xs-4">stylesheet.css
			</div>
			<div class="col-xs-2"><?php
		if (@file_exists('../includes/themes/default/css/stylesheet.css') && @is_writable('../includes/themes/default/css/stylesheet.css')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else if (is_writable('..')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else {
			echo '<span class="label label-danger">' . $_language->module['unwriteable'] . '</span><br>
		<div class="alert alert-danger">' . $_language->module['stylesheet_error'] . '</div>';
		} ?>
			</div>
			</div>

			<div class="card col-xs-6">
			<div class="col-xs-4"><?=$_language->module['setting_chmod']; ?>
			</div>
			<div class="col-xs-2"><?php
		$chmodfiles = Array('/system/sql.php', '/includes/themes/default/css/stylesheet.css', '/images/avatars', '/images/flags', '/images/squadicons', '/images/games', '/images/userpics', '/includes/plugins', '/tmp/');
		sort($chmodfiles);
		$error = array();
		foreach ($chmodfiles as $file) {
	if (!is_writable('../' . $file)) {
		echo '-> ' . $file . '';
	if (!@chmod('../' . $file, 0777)) $error[] = $file . '';
		}
	}
	?><?php
		if (count($error)) {
			sort($error);
			echo '<span class="label label-danger">' . $_language->module['chmod_error'] . '</span>:';
			foreach ($error as $value)
				echo '<span class="label label-danger">' . $value . '</span>';
			} else echo '<span class="label label-success">' . $_language->module['successful'] . '</span>';
		?>
			</div>
			</div>



            <input type="hidden" name="hp_url" value="<?=CurrentUrl();?>">
				<?php if (!$fatal_error) { ?>
                    <div class="pull-right"><a class="btn btn-primary" href="javascript:document.ws_install.submit()">continue</a></div>
                <?php } ?>
			</div>
		</div>
    </div>
</div> <!-- end row -->
