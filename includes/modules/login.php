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

$_language->readModule('login');

if ($loggedin) {
            
    $_language->readModule('login');
    $login_lang = $_language->module;
    $_language->readModule('loginoverview');

if ($userID && !isset($_GET[ 'userID' ]) && !isset($_POST[ 'userID' ])) {
    $data_array = array();
    $data_array['$title'] = $_language->module[ 'overview' ];
    $template = $tpl->loadTemplate("loginoverview","head", $data_array);
    echo $template;

    $ds =
        mysqli_fetch_array(safe_query("SELECT registerdate FROM `" . PREFIX . "user` WHERE userID='" . $userID . "'"));
    $username = '<a href="index.php?site=profile&amp;id=' . $userID . '">' . getnickname($userID) . '</a>';
    $lastlogin = getformatdatetime($_SESSION[ 'ws_lastlogin' ]);
    $registerdate = getformatdatetime($ds[ 'registerdate' ]);

    

    //clanmember/admin/referer

    

    if (isanyadmin($userID)) {
        $admincenterpic =
            '<a href="admin/admincenter.php" target="_blank">
                <i class="fa fa-cogs fa-2x" alt="Admincenter"></i><br>
                '.$login_lang[ 'admin' ].'
            </a>';
    } else {
        $admincenterpic = '';
    }

    if (isset($_SESSION[ 'referer' ])) {
        $referer_uri = '<a class="btn" href="' . $_SESSION[ 'referer' ] . '">
            <i class="fa fa-chevron-left"></i> ' .
            $_language->module[ 'back_last_page' ] . '</a>';
        unset($_SESSION[ 'referer' ]);
    } else {
        $referer_uri = '';
    }

   
   

    $data_array = array();
    $data_array['$_modulepath'] = MODULE;
    #$data_array['$_modulepath'] = substr(MODULE, 0, -1);
    $data_array['$username'] = $username;
    $data_array['$lastlogin'] = $lastlogin;
    $data_array['$registerdate'] = $registerdate;
    $data_array['$referer_uri'] = $referer_uri;
    $data_array['$admincenterpic'] = $admincenterpic;
    
            $data_array['$buddy_list'] = $_language->module[ 'buddy_list' ];
            $data_array['$messenger'] = $_language->module[ 'messenger' ];
            $data_array['$edit_account'] = $_language->module[ 'edit_account' ];
            $data_array['$logout'] = $_language->module[ 'logout' ];
            $data_array['$user'] = $_language->module[ 'user' ];
            $data_array['$last_login'] = $_language->module[ 'last_login' ];
            $data_array['$registered'] = $_language->module[ 'registered' ];
            $data_array['$informations'] = $_language->module[ 'informations' ];
            $data_array['$menu'] = $_language->module[ 'menu' ];
            
    $template = $tpl->loadTemplate("loginoverview","content", $data_array);
    echo $template;
} else {
    echo $_language->module[ 'you_have_to_be_logged_in' ];
}

} else {
    //set sessiontest variable (checks if session works correctly)
    $_SESSION[ 'ws_sessiontest' ] = true;
    
    $data_array=array();
	$data_array['$_modulepath'] = substr(MODULE, 0, -1);
    $data_array['$login_titel'] = $_language->module[ 'login_titel' ];
    $data_array['$login'] = $_language->module[ 'login' ];
    $data_array['$lang_register'] = $_language->module[ 'register' ];
    $data_array['$info'] = $_language->module[ 'info' ];
    $data_array['$info1'] = $_language->module[ 'info1' ];
    $data_array['$info2'] = $_language->module[ 'info2' ];
    $data_array['$info3'] = $_language->module[ 'info3' ];
    $data_array['$info4'] = $_language->module[ 'info4' ];
	$data_array['$register_now'] = $_language->module[ 'register_now' ];
	$data_array['$lost_password'] = $_language->module[ 'lost_password' ];
	$loginform = $tpl->loadTemplate("login","content", $data_array);
    echo $loginform;
}
