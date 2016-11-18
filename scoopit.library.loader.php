<?php
include_once dirname(__FILE__).'/external.libraries/oauth/oauth2-server-php/src/OAuth2/Autoloader.php';
include_once "dna.libraries/utilities/dna.main.utility.php";
include_once "dna.libraries/utilities/dna.misc.utility.php";
include_once "dna.libraries/services/dna.main.service.php";
//function to include all files in the libraries available for module in runtime
DnaMiscUtility::doRequireFiles(dirname(__FILE__).'/dna.libraries',
	array("install.php",),".php");