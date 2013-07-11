<?php

/**
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 3 of the License, or (at
 *   your option) any later version.
 * 
 *   This program is distributed in the hope that it will be useful, but
 *   WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *   General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 *   @author          Black Cat Development
 *   @copyright       2013, Black Cat Development
 *   @link            http://blackcat-cms.org
 * @license			http://www.gnu.org/licenses/gpl.html
 *   @category        CAT_Core
 *   @package         CAT_Core
 *
 */

if (defined('CAT_PATH')) {
    if (defined('CAT_VERSION')) include(CAT_PATH.'/framework/class.secure.php');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
    include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php');
} else {
    $subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));    $dir = $_SERVER['DOCUMENT_ROOT'];
    $inc = false;
    foreach ($subs as $sub) {
        if (empty($sub)) continue; $dir .= '/'.$sub;
        if (file_exists($dir.'/framework/class.secure.php')) {
            include($dir.'/framework/class.secure.php'); $inc = true;    break;
	}
	}
    if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}

$backend = CAT_Backend::getInstance('Addons', 'modules_install');
$user    = CAT_Users::getInstance();
$val     = CAT_Helper_Validate::getInstance();

$action	 = $val->sanitizePost('action');
$module	 = $val->sanitizePost('file');
$type    = $val->sanitizePost('type').'s';

$js_back = CAT_ADMIN_URL . '/addons/index.php';

if ( !in_array( $action, array('install', 'upgrade') ) )
{
	die(header('Location: '.CAT_ADMIN_URL.'/'.CAT_BACKEND_PATH.'/addons/index.php'));
}
if ( $module == '' || !( strpos($module, '..') === false ) )
{
	die(header('Location: '.CAT_ADMIN_URL.'/'.CAT_BACKEND_PATH.'/addons/index.php'));
}

// validate
$path = CAT_Helper_Directory::sanitizePath(CAT_PATH.'/'.$type.'/'.$module.(($type=='languages')?'.php':''));
$info = CAT_Helper_Addons::checkInfo($path);

if ( ! is_array($info) || ! count($info) )
{
    $backend->print_error(
          $backend->lang()->translate(
              'Unable to {{ action }} {{ type }} {{ module }}!',
              array('action'=>$action,'type'=>substr( $type, 0, -1 ),'module'=>$path)
          )
        . ': <tt>"'
        . htmlentities(basename($path)).'/'.$action.'.php"</tt> does not exist',
        $js_back
    );
}

if ( $type != 'languages' )
{
    // this prints an error page if prerequisites are not met
    $precheck_errors = CAT_Helper_Addons::preCheckAddon( NULL, $path, false );
    if ( $precheck_errors != '' && ! is_bool($precheck_errors) )
    {
        $backend->print_error($backend->lang()->translate(
            'Invalid installation file. {{error}}',
            array('error'=>$precheck_errors)
        ));
        return false;
    }
}

if ( ! CAT_Helper_Addons::installModule($path) )
{
    // the method will print the error, but to be sure...
    $backend->print_error(
          $backend->lang()->translate(
              'Unable to {{ action }} {{ type }} {{ module }}!',
              array('action'=>$action,'type'=>substr( $type, 0, -1 ),'module'=>$info['module_name'])
          ),
        $js_back
    );
}

switch ($action)
{
	case 'install':
	case 'upgrade':
		$backend->print_success( 'Done', $js_back);
		break;
	default:
		$backend->print_error( 'Action not supported', $js_back );
}

// Print admin footer
$backend->print_footer();

?>