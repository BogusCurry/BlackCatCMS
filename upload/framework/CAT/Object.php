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
 *   @category        CAT_Core
 *   @package         CAT_Core
 *
 */

/**
 *
 * Base class for all Helper classes; provides some common methods
 *
 */
if ( ! class_exists( 'CAT_Object', false ) ) {

    if ( ! class_exists( 'CAT_Helper_KLogger', false ) ) {
		@include dirname(__FILE__).'/Helper/KLogger.php';
	}

	class CAT_Object
	{
	
	    protected $debugLevel      = 8; // 8 = OFF
	    // array to store config options
        protected $_config         = array( 'loglevel' => 8 );
        // Language helper object handle
        protected $lang;
        // database handle
        protected $db;
        // KLogger object handle
        private   $logObj;
        
        // Log levels
        const EMERG  = 0;  // Emergency: system is unusable
	    const ALERT  = 1;  // Alert: action must be taken immediately
	    const CRIT   = 2;  // Critical: critical conditions
	    const ERR    = 3;  // Error: error conditions
	    const WARN   = 4;  // Warning: warning conditions
	    const NOTICE = 5;  // Notice: normal but significant condition
	    const INFO   = 6;  // Informational: informational messages
	    const DEBUG  = 7;  // Debug: debug messages
    	const OFF    = 8;

        /**
         * inheritable constructor; allows to set object variables
         **/
        public function __construct ( $options = array() ) {
            if ( is_array( $options ) ) {
                $this->config( $options );
            }
            // allow to set log level on object creation
            if ( isset( $this->_config['loglevel'] ) ) {
                $this->debugLevel = $this->_config['loglevel'];
            }
            // allow to enable debugging on object creation; this will override
            // 'loglevel' if both are set
            if ( isset( $this->_config['debug'] ) ) {
                $this->debug(true);
            }
		}   // end function __construct()
		
		public function __destruct() {}
		
		public function lang()
		{
            if ( ! is_object($this->lang) )
            {
                if ( ! class_exists( 'CAT_Helper_I18n', false ) )
		    {
					@include dirname(__FILE__).'/Helper/I18n.php';
				}
                $this->lang = CAT_Helper_I18n::getInstance();
		}
            return $this->lang;
        }   // end function lang()
		
		/**
         * set config values
         *
         * This method allows to set object variables at runtime.
         * If $option is an array, the array keys are treated as object var
         * names, the array values as their values. The second param $value
         * is ignored in this case.
         * If $option is a string, it is treated as object var name; in this
         * case, $value must be set.
         *
         * @access public
         * @param  mixed    $option
         * @param  string   $value
         * @return void
         *
         **/
        public function config( $option, $value = NULL ) {
			if ( is_array( $option ) )
			{
                $this->_config = array_merge( $this->_config, $option );
            }
            else
			{
                $this->_config[$option] = $value;
            }
            return $this;
        }   // end function config()
        
        /**
         * create a guid; used by the backend, but can also be used by modules
         *
         * @access public
         * @param  string  $prefix - optional prefix
         * @return string
         **/
        public static function createGUID($prefix='')
        {
            if(!$prefix||$prefix='') $prefix=rand();
            $s = strtoupper(md5(uniqid($prefix,true)));
            $guidText =
                substr($s,0,8) . '-' .
                substr($s,8,4) . '-' .
                substr($s,12,4). '-' .
                substr($s,16,4). '-' .
                substr($s,20);
            return $guidText;
        }   // end function createGUID()
        
        /**
         * prints a formatted error message
         *
         * @access public
         * @param  string  $message - error message
         * @param  mixed   $args - additional args to print
         *
         **/
        public function printError( $message = NULL, $link = 'index.php', $args = NULL ) {
            $print_footer = false;
            $caller       = debug_backtrace();
            // remove first item (it's the printError() method itself)
            array_shift($caller);
            // if called by printFatalError(), shift again...
            if ( isset( $caller[0]['function'] ) && $caller[0]['function'] == 'printFatalError' ) {
                array_shift($caller);
            }
            $caller_class = isset( $caller[0]['class'] )
                          ? $caller[0]['class']
                          : NULL;

            if (true === is_array($message)){
    			$message = implode("<br />", $message);
    		}

            if ( is_object($this) && isset($this->lang) )
            {
                $message = $this->lang->translate($message);
            }

            // avoid headers already sent error
            if ( ! headers_sent() ) {
                $print_footer = true;
                echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <title>LEPTON v2.0 Black Cat Edition - '.$caller_class.' Fatal Error</title>
  </head>
  <body>';
            }

            // if we're able to use the template parser...
            global $parser;
            if ( ! is_object($parser) )
            {
                echo "<div id=\"caterror\">\n",
                 "  <h1>$caller_class Fatal Error</h1><br /><br />\n",
                 "  <div style=\"color: #FF0000; font-weight: bold; font-size: 1.2em;\">\n",
                     "  $message\n";
            }
            else
            {
                $parser->setPath(CAT_THEME_PATH.'/templates');
				$parser->output('error.lte', array('MESSAGE'=>$message,'LINK'=>$link));
            }

            if ( $args ) {
                $dump = print_r( $args, 1 );
                $dump = preg_replace( "/\r?\n/", "\n          ", $dump );
                echo "<br />\n";
                echo "<pre>\n";
                echo "          ", $dump;
                echo "</pre>\n";
            }

            // remove path info from file
            $file = ( isset($caller[1]) && isset($caller[1]['file']) )
                  ? basename( $caller[1]['file'] )
                  : 'unknown';

            echo "<br /><br /><span style=\"font-size: smaller;\">[ ",
                 $file, ' : ',
                 ( ( isset($caller[1]) && isset($caller[1]['line'])     ) ? $caller[1]['line']     : '-' ),
                 ' : ',
                 ( ( isset($caller[1]) && isset($caller[1]['function']) ) ? $caller[1]['function'] : '-' ),
                 " ]</span><br />\n";

            if ( $this->debugLevel == self::DEBUG ) {
                echo "<h2>Debug backtrace:</h2>\n",
                     "<textarea cols=\"100\" rows=\"20\" style=\"width: 100%;\">";
                print_r( $caller );
                echo "</textarea>";
            }

            echo "  </div>\n</div><!-- id=\"leperror\" -->\n";

            if ( $print_footer ) {
                echo "</body></html>\n";
            }

        }   // end function printError()
        
        /**
         * wrapper to printError(); print error message and exit
         *
         * see printError() for @params
         *
         * @access public
         *
         **/
		public function printFatalError( $message = NULL, $args = NULL ) {
		    $this->printError( $message, $args );
		    exit;
		}   // end function printFatalError()

        /**
         * sanitize path (remove '/./', '/../', '//')
         *
         * @access public
         * @param  string  $path - path to sanitize
         * @return string
         *
         **/
        public function sanitizePath( $path )
        {
			$path       = str_replace( '\\', '/', $path );
            $path       = preg_replace('~/\./~', '/', $path); // bla/./bloo ==> bla/bloo
            // resolve /../
            // loop through all the parts, popping whenever there's a .., pushing otherwise.
            $parts      = array();
            foreach ( explode('/', preg_replace('~/+~', '/', $path)) as $part )
            {
                if ($part === ".." || $part == '')
                {
                    array_pop($parts);
                }
                elseif ($part!="")
                {
                    $parts[] = $part;
                }
            }
            $new_path = implode("/", $parts);
            // windows
            if ( ! preg_match( '/^[a-z]\:/i', $new_path ) ) {
				$new_path = '/' . $new_path;
			}
            return $new_path;
        }   // end function sanitizePath()
        
/*******************************************************************************
 * LOGGING / DEBUGGING
 ******************************************************************************/
        
        /**
         * enable or disable debugging at runtime
         *
         * @access public
         * @param  boolean  enable (TRUE) / disable (FALSE)
         *
         **/
		public function debug( $bool ) {
		    if ( $bool === true ) {
               	$this->debugLevel = 7; // 7 = Debug
			}
			else {
			    $this->debugLevel = 8; // 8 = OFF
			}
        }   // end function debug()

        /**
         * returns a database connection handle
         *
         * This function must be used by all classes, as we plan to replace
         * the database class in later versions!
         *
         * @access public
         * @return object
         **/
        public function db()
        {
            if ( ! $this->db || ! is_object($this->db) )
            {
                if ( ! class_exists('database') )
                    @include CAT_PATH.'/framework/class.database.php';
                $this->db = new database();
            }
            return $this->db;
        }   // end function db()

        /**
      	 * Accessor to KLogger class; this makes using the class significant faster!
      	 *
      	 * @access public
      	 * @return object
      	 *
      	 **/
      	public function log () {
            if ( $this->debugLevel < 8 ) { // 8 = OFF
                if ( ! is_object( $this->logObj ) ) {
                    $debug_dir = $this->sanitizePath( CAT_PATH.'/temp/logs' );
                    if ( ! file_exists( $debug_dir ) ) {
                        mkdir( $debug_dir, 0777 );
                    }
                    $this->logObj = CAT_Helper_KLogger::instance( $debug_dir, $this->debugLevel );
                }
                return $this->logObj;
            }
            return $this;
      	}   // end function log ()

		/**
		 * Fake KLogger access methods if debugLevel is set to 8 (=OFF)
		 **/
  		public function logInfo  () {}
		public function logNotice() {}
		public function logWarn  () {}
		public function logError () {}
		public function logFatal () {}
		public function logAlert () {}
		public function logCrit  () {}
		public function logEmerg () {}
		public function logDebug () {}

	}
}