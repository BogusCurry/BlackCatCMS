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
 *   @license         http://www.gnu.org/licenses/gpl.html
 *   @category        CAT_Modules
 *   @package         wysiwyg_admin
 *
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {
	include(WB_PATH.'/framework/class.secure.php');
} else {
	$root = "../";
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= "../";
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) {
		include($root.'/framework/class.secure.php');
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

if ( !defined('WB_PATH')) die(header('Location: ../../index.php'));

$debug = false;
if (true === $debug) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL|E_STRICT);
}

abstract class c_editor_base
{

    private $default_skin = NULL;
    private $default_height = '250px';
    private $default_width  = '100%';

    abstract public function getSkinPath();
    abstract public function getToolbars();
    abstract public function getAdditionalSettings();

    private function get($name,&$config)
    {
        if(isset($config[$name]))
        {
            $val = $config[$name];
            unset($config[$name]);
            return $val;
        }
    }

    public function getHeight(&$config)
    {
        $val = $this->get('height',$config);
        return ( $val != '' ) ? $val : $this->default_height;
    }

    public function getWidth(&$config)
    {
        $val = $this->get('width',$config);
        return ( $val != '' ) ? $val : $this->default_width;
    }

    public function getSkin(&$config)
    {
        $val = $this->get('skin',$config);
        return ( $val != '' ) ? $val : $this->default_skin;
    }

    public function getSkins($skin_path)
    {
        global $admin;
        $admin->get_helper('Directory')->setRecursion(false);
        $skins = $admin->get_helper('Directory')->getDirectories($skin_path,$skin_path.'/');
        $admin->get_helper('Directory')->setRecursion(true);
        return $skins;
    }
}

?>