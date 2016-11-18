<?php
class DnaMainUtility {

    protected $em;
    protected $sm;

    private static $instance = NULL;
	private $pdoDb = NULL;
	private $oauth_server = NULL;

    public static function getInstance()
    {
        drupal_session_start();

        if(self::$instance==NULL){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
		global $databases;

		$host = $databases['default']['default']['host'];

		$dbname = $databases['default']['default']['database'];
		$username = $databases['default']['default']['username'];
		$password = $databases['default']['default']['password'];

		$this->pdoDb = new PDO('mysql:host='.$host.';dbname='.$dbname.'',$username , $password);

		$dsn      = 'mysql:dbname='.$dbname.';host='.$host;

// error reporting (this is a demo, after all!)
		ini_set('display_errors',1);error_reporting(E_ALL);

// Autoloading (composer is preferred, but for this example let's just do this)
		//require_once('oauth2-server-php/src/OAuth2/Autoloader.php');
		OAuth2\Autoloader::register();

// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
		$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

// Pass a storage object or array of storage objects to the OAuth2 server class
		$server = new OAuth2\Server($storage);

// Add the "Client Credentials" grant type (it is the simplest of the grant types)
		$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

// Add the "Authorization Code" grant type (this is where the oauth magic happens)
		$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

		$this->oauth_server = $server;
    }

    //function to check if a string ends with argument
    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return false;
        }

        return (substr($haystack, -$length) === $needle);
    }

    //function to delete the file on the file repository
    public static function deletefile($filename) {
        //$file = $filename;

        if (file_exists($filename)) {
            //die($filename);
            unlink($filename);
        }

        return true;
    }

    //function to sanitize the string
    public static function sanitize($var) {
        try {

            //$var = ''.$var.'';
            $newvar = strip_tags(substr($var, 0)); //remove tags
            //none works due to zend architecture
            //$newvar = mysql_escape_string($newvar); //remove mysql problems when we downgrade lets replace this with
            //$newvar = \Stdlib::mysql_real_escape_string($newvar);//remove mysql problems when we upgrade lets replace this with
            //$newvar = escapeshellarg($newvar);//remove mysql problems
            //$newvar = escapeshellcmd($newvar);//remove mysql problems
            if ($newvar == '' && !is_numeric($var))
                $newvar = '';
            else if ($newvar == '' && $var==0)
                $newvar = 0;
            else if($newvar == '')
                $newvar = NULL;
        } catch (\Exception $ex) {

            $newvar = NULL;
            //die($ex);
        }
        return $newvar;
    }

    //function to check if a string starts with something
    public static function startsWith($haystack,$needle){
        if(substr( $haystack, 0, strlen($needle) ) === $needle)
        {
            return true;
        }
        return false;
    }

    //function to get the current user ip
    public static function get_user_ip() {
        if (isSet($_SERVER)) {
            if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }

    public static function http_post($url, $param, $user_agent, $status = NULL, $wait = 3) {
        $time = microtime(true);
        $expire = $time + $wait;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //
        if (!$head) {//echo $url.$param;die();
            return FALSE;
        }

        if ($status === NULL) {
            if ($httpCode < 400) {
                return TRUE;
            } else {
                return FALSE;
            }
        } elseif ($status == $httpCode) {
            return TRUE;
        }

        return FALSE;
    }

    public static function sortArrayOfObject($object,$prop)
    {
        if ($object != NULL) {
            usort($object, function($a, $b) use ($prop) {
                return $a->$prop > $b->$prop ? 1 : -1;
            });
        }

        return $object;
    }

    public static function dSortArrayOfObject($object,$prop)
    {
        if ($object != NULL) {
            usort($object, function($a, $b) use ($prop) {
                return $a->$prop < $b->$prop ? 1 : -1;
            });
        }

        return $object;
    }

    public static function sortArrayOfArray($object,$prop)
    {
        if ($object != NULL) {
            usort($object, function($a, $b) use ($prop) {
                return $a[$prop] > $b[$prop] ? 1 : -1;
            });
        }

        return $object;
    }

    public static function dSortArrayOfArray($object,$prop)
    {
        if ($object != NULL) {
            usort($object, function($a, $b) use ($prop) {
                return $a[$prop] < $b[$prop] ? 1 : -1;
            });
        }

        return $object;
    }

    public static function url_exists($url) {
        $file_headers = @get_headers($url);
        $exists = false;

        if(is_array($file_headers) && !empty($file_headers)){
            if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
                $exists = false;
            }
            else {
                $exists = $file_headers;
            }
        }
        else
            $exists = false;

        return $exists;
    }

    public static function clearSession($session_name)
    {
        self::deleteSessionObject($session_name);
    }

    public static function getSessionObject($key)
    {
        if (!empty($_SESSION[DNA_SESSION_NAME]) && isset($_SESSION[DNA_SESSION_NAME][$key])) {
            return $_SESSION[DNA_SESSION_NAME][$key];
        }
        else
        {
            return NULL;
        }
    }

    public static function setSessionObject($key,$value)
    {
        $_SESSION[DNA_SESSION_NAME][$key] = $value ;
        return $value;
    }

    public static function createSessionObject($key)
    {
        $retVal = self::getSessionObject($key);
        if(!$retVal){
            $retVal = self::setSessionObject($key,new stdClass());
        }
        return $retVal;
    }

    public static function deleteSessionObject($key)
    {
        if (!empty($_SESSION[DNA_SESSION_NAME]) && isset($_SESSION[DNA_SESSION_NAME][$key])) {
            unset($_SESSION[DNA_SESSION_NAME][$key]);
        }
    }

    public function getPdoDb()
	{
		return $this->pdoDb;
	}

	public static function getSeverScheme()
	{
		return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? 'https://' : 'http://');
	}

    public static function installOauthTables()
	{
		$instance = self::getInstance();

		try{
			$db = $instance->getPdoDb();
			$sql = file_get_contents(dirname(__FILE__) . '/res/oauth_mysql_for_scoopit.sql');
			$stmt = $db->prepare($sql);
			$stmt->execute();

		}
		catch (Exception $ex)
		{
			echo $ex;
		}
	}

    public static function getOauth()
    {
        drupal_session_start();

        //get the OAuth server
		$instance = self::getInstance();

        return $instance->oauth_server;
    }

	public static function verifyOauthRequest()
	{
		$oAuthObject = self::getOauth();

// Handle a request to a resource and authenticate the access token
		if (!$oAuthObject->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
			$oAuthObject->getResponse()->send();
			die;
		}
	}
}
