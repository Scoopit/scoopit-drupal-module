<?php

class DnaMiscUtility extends DnaMainUtility {
	private static $instance = NULL;

	public static function getInstance()
	{
		if(self::$instance==NULL){
			self::$instance = new self();
		}

		return self::$instance;
	}
	
    public function __construct() {
        parent::__construct();
		
    }
	
	public static function getDirectoriesInFolder($path,$recursive=false)
	{
		$dir = $path;
        $returnFiles = array();

        $files = scandir($dir);
		
        foreach ($files as $file) {
			if(is_dir($path.'/'.$file) && $file!="." && $file!=".."){
				$returnFiles[] = $path.'/'.$file;
				if($recursive)
				{
					$innerDirs = DnaMiscUtility::getDirectoriesInFolder($path.'/'.$file,$recursive);
					array_push($returnFiles, $innerDirs);
				}
			}
        }
		
		return $returnFiles;
	}
	
	public static function getFilesInFolder($path,$recursive=false,$file_type="*")
	{
		$dir = $path;
        $returnFiles = array();

        $files = scandir($dir);

        foreach ($files as $file) {
			if($file_type=="*" && !is_dir($path.'/'.$file) && $file!="." && $file!=".."){
				$returnFiles[] = $path.'/'.$file;
			}
            else if (DnaMiscUtility::endsWith($path.'/'.$file, $file_type) && !is_dir($file) && $file!="." && $file!="..") {
				$returnFiles[] = $path.'/'.$file;
            }
			else if(is_dir($path.'/'.$file) && $file!="." && $file!="..")
			{
				if($recursive)
				{
					$innerFiles = DnaMiscUtility::getFilesInFolder($path.'/'.$file,$recursive,$file_type);
					array_push($returnFiles, $innerFiles);
				}
			}
        }
		
        return $returnFiles;
	}

    public static function uploadContentFile($content_id,$file_id, $file, $filename, $access_level=1, $maxFile = 30000000, $allowedExt = array())
    {
        if($access_level==1)
        {
            $directory = self::getPublicApplicationUploadsDir()."/Files/".$content_id."/".$file_id."/";
        }
        else
        {
            $directory = self::getPrivateApplicationUploadsDir()."/Files/".$content_id."/".$file_id."/";
        }

        $target_path = str_replace('\\','/',$directory);
        if($filename==NULL || trim($filename."")=="")
            return DnaMiscUtility::uploadfile($target_path, $file, $allowedExt, "fle".$content_id.date("Y-m-d.H.i.s"),false,$maxFile);
        else
        {
            //overwrite existing file
            $filenameSplit = explode('.',$filename);
            $newUploadedFile = DnaMiscUtility::uploadfile($target_path, $file, $allowedExt, $filenameSplit[0], true,$maxFile);

            //else delete previous file
            if($newUploadedFile!=$filename)
                DnaMiscUtility::deletefile($target_path.$filename);

            return $newUploadedFile;
        }
    }

	public static function deleteContentFileUpload($content_id,$file_id, $access_level=1,$filename=NULL)
	{
		if($access_level==1)
		{
			$directory = self::getPublicApplicationUploadsDir()."/Files/".$content_id."/".$file_id."/";
		}
		else
		{
			$directory = self::getPrivateApplicationUploadsDir()."/Files/".$content_id."/".$file_id."/";
		}
		
		$target_path = str_replace('\\','/',$directory);
		DnaMiscUtility::deletefile($target_path.$filename);
	}

    public static function renderFile($file_name)
    {
        $downloadurl = $file_name;
        $splited_filename = explode(".",basename($downloadurl));
        $ext = strtolower($splited_filename [sizeof($splited_filename)-1]);

        $content_type = DnaDocsUtility::get_file_content_type_from_extension('.'.$ext);

        header("Content-type: {$content_type}");

        readfile($downloadurl);
    }


	public static function generateSessionUrlHash($url)
	{
		//$session_obj = new Container('user_session');
		return md5($url.session_id());
	}

	public static function validateSessionUrlHash($hash,$url)
	{

		if(md5($url.session_id())==$hash)
		{
			return true;
		}

		return false;
	}

	public function testForHuman($arg)
	{
		$session_obj = self::createSessionObject('user_session');
		if($session_obj->answer==$arg)
		{
			$testResult = true;
		}
		else{
			$testResult = false;
		}

		$session_obj->question_answered = $testResult;
		$this->generateHumanTest();
		return $testResult;
	}

	public function generateHumanTest($regenerate=false){
		$session_obj = self::createSessionObject('user_session');
		if($regenerate || ($session_obj->question_answered || !$session_obj->question)){
			$session_obj->question_cal=array();
			$randNumber = rand(0,60);
			$session_obj->question_cal[0] = rand(1,9);
			$session_obj->question_cal[1] = rand(1,9);
			$numberConversionObj = DnaNumberConversionUtility::getInstance();
			if($randNumber>40)
			{
				$session_obj->question_cal[2] = 1;
				$session_obj->answer = $session_obj->question_cal[0] + $session_obj->question_cal[1];
				$session_obj->question = $numberConversionObj->convertNumber($session_obj->question_cal[0])." + ".$numberConversionObj->convertNumber($session_obj->question_cal[1])." = ";
			}
			else if($randNumber>20)
			{
				$session_obj->question_cal[2] = 2;
				if($session_obj->question_cal[1]>$session_obj->question_cal[0])
				{
					$session_obj->answer = $session_obj->question_cal[1] - $session_obj->question_cal[0];
					$session_obj->question = $numberConversionObj->convertNumber($session_obj->question_cal[1])." - ".$numberConversionObj->convertNumber($session_obj->question_cal[0])." = ";
				}
				else
				{
					$session_obj->answer = $session_obj->question_cal[0] - $session_obj->question_cal[1];
					$session_obj->question = $numberConversionObj->convertNumber($session_obj->question_cal[0])." - ".$numberConversionObj->convertNumber($session_obj->question_cal[1])." = ";
				}
			}
			else
			{
				$session_obj->question_cal[2] = 3;
				$session_obj->answer = $session_obj->question_cal[0] * $session_obj->question_cal[1];
				$session_obj->question = $numberConversionObj->convertNumber($session_obj->question_cal[0])." x ".$numberConversionObj->convertNumber($session_obj->question_cal[1])." = ";
			}
			$session_obj->question_answered = false;
		}

		return $session_obj->question;
	}
    
	public static function getDayNameFromLookUp($lookupDataDays,$dayValue)
	{
		foreach($lookupDataDays as $day)
		{
			if($day['lookup_value']==$dayValue)
			{
				$dayName = $day['lookup_name'];
				break;
			}
		}
		
		return $dayName;
	}
	
	public static function getMonthNameFromLookUp($lookupDataMonths,$monthValue)
	{
		foreach($lookupDataMonths as $month)
		{
			if($month['lookup_value']==$monthValue)
			{
				$monthName = $month['lookup_name'];
				break;
			}
		}
		
		return $monthName;
	}
	
	public static function convert_number_to_words($number) {
   
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);
	   
		if (!is_numeric($number)) {
			return false;
		}
	   
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ($number < 0) {
			return $negative . self::convert_number_to_words(abs($number));
		}
	   
		$string = $fraction = null;
	   
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
	   
		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . self::convert_number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= self::convert_number_to_words($remainder);
				}
				break;
		}
	   
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
	   
		return $string;
	}


    public static function doIncludeFiles($dir,$ignore="--",$type=".php")
    {
        if(!is_array($dir))
        {
            if(is_dir($dir)){
                $requireArray = DnaMiscUtility::getFilesInFolder($dir,true,$type);
            }
            else
            {
                $requireArray = $dir;
            }
        }
        else
        {
            $requireArray = $dir;
        }

        if($requireArray && !empty($requireArray) && is_array($requireArray)) {
            foreach ($requireArray as $require) {
                if(is_array($require))
                {
                    self::doIncludeFiles($require,$ignore,$type);
                }
                else if(!self::endsWith($require,$ignore))
                {
                    include_once $require;
                }

            }
        }
        else if( $requireArray && file_exists($requireArray) && !self::endsWith($requireArray,$ignore))
        {
            include_once $requireArray;
        }
    }

    public static function doRequireFiles($dir, $ignores=array ("--"), $type=".php")
    {
        if(!is_array($dir))
        {
            if(is_dir($dir)){
                $requireArray = DnaMiscUtility::getFilesInFolder($dir,true,$type);
            }
            else
            {
                $requireArray = $dir;
            }
        }
        else
        {
            $requireArray = $dir;
        }

        if($requireArray && !empty($requireArray) && is_array($requireArray)) {
            foreach ($requireArray as $require) {
                if(is_array($require))
                {
                    self::doRequireFiles($require,$ignores,$type);
                }
                else
                {
					$ignoring = false;
                	foreach ($ignores as $ignore)
					{
						if(self::endsWith($require,$ignore))
						{
							$ignoring = true;
						}
					}

					if(!$ignoring)
					{
						require_once $require;
					}

                }
            }
        }
        else if( $requireArray && file_exists($requireArray))
        {
			$ignoring = false;
			foreach ($ignores as $ignore)
			{
				if(self::endsWith($requireArray,$ignore))
				{
					$ignoring = true;
				}
			}

			if(!$ignoring)
			{
				require_once $requireArray;
			}

        }
    }

	public static function getAssociatedArrayKeyDataFromObject($contents)
	{
		if(is_array($contents)){
			$newContents = array();
			foreach ($contents as $item)
				$newContents[] = (array) $item;//get_object_vars($item);
		}
		else
		{
			if(is_object($contents)){
				$newContents = (array) $contents;//get_object_vars($contents);
			}
			else
			{
				$newContents = $contents;
			}
		}


		return $newContents;
	}

	private function objectToArray ($object) {
		if(!is_object($object) && !is_array($object))
			return $object;

		return array_map('objectToArray', (array) $object);
	}
}
