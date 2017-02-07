<?php
/**
 * Created by PhpStorm.
 * User: lecooper
 * Date: 01/08/2016
 * Time: 18:46
 */
global $base_path;
define('DNA_SESSION_NAME','DNA_SESSION');
define('SERVER_ENVIRONMENT','Test');
define('MAX_SCOOP_IT_OBJECT',50);
define('SCOOPIT_API_LOCAL_SERVER',$_SERVER["HTTP_HOST"].'/'.$base_path);
define('SCOOPIT_API_SERVER_SCHEME',((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? 'https://' : 'http://'));
define('SCOOPIT_API_VERSION','1.1');

require_once "scoopit.library.loader.php";
//functions used for web request
function scoop_it_api_services()
{
	$returnObj = array(
		'#markup' => ""
	);
	return $returnObj;
}

/*
 * Image Request scoop_it_data:
{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0}
Single Post Request scoop_it_data:
{"id":1,"publicationDate":"d-m-Y H:i:s","title":"Title for post 1","summary":"This is summary for post 1","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":["tag1","tag2","tag3"],"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 1 content"}
Multiple Post Request scoop_it_data:
[{"id":1,"publicationDate":"d-m-Y H:i:s","title":"Title for post 1","summary":"This is summary for post 1","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":["tag1","tag2","tag3"],"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 1 content"},{"id":2,"publicationDate":"d-m-Y H:i:s","title":"Title for post 2","summary":"This is summary for post 2","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":null,"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 2 content"},{"id":3,"publicationDate":"d-m-Y H:i:s","title":"Title for post 3","summary":"This is summary for post 3","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":null,"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 3 content"},{"id":4,"publicationDate":"d-m-Y H:i:s","title":"Title for post 4","summary":"This is summary for post 4","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":null,"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 4 content"}]
 */
function scoop_it_api_add()
{
	addResponseHeader();
	$response = new DnaMainResponse();
	DnaMainUtility::verifyOauthRequest();

	$dataObjectParam = isset($_POST["scoop_it_data"])?$_POST["scoop_it_data"]:NULL;
	$dataObjects = json_decode($dataObjectParam);

	if($dataObjects != NULL )
	{

		if(!is_array($dataObjects)){
			$dataObjects = array($dataObjects);
		}

		$object_count = sizeof($dataObjects);

		if($object_count<=MAX_SCOOP_IT_OBJECT && $object_count>0){

			$dataMappingObject = DnaContentMapService::getInstance();
			$processedObjects = array();

			$localObjectArguments = array();
			//create the node object arguments
			foreach ($dataObjects as $remoteObject)
			{
				$nodeCreationArgument = $dataMappingObject->mapRemoteObjectToLocalArgumentForAction($remoteObject);
				$localObjectArguments[] = $nodeCreationArgument;
			}

			//create the node objects
			$nodeObjectsResponse = $dataMappingObject->createNodes($localObjectArguments);

			//convert the node objects to remote objects
			$i=0;
			foreach ($nodeObjectsResponse as $nodeObject)
			{
				$retObj = $dataMappingObject->mapLocalObjectToRemoteObject($nodeObject,$dataObjects[$i]->scoopit_type);
				if($retObj)
					$processedObjects[] = $retObj;
				$i++;
			}

			$response->responseCode = "0";
			$contents = DnaMiscUtility::getAssociatedArrayKeyDataFromObject($processedObjects);
			$response->responseMessage = "success";
			$response->responseData = $contents;
		}
		else
		{

			$response->responseCode = "-1";
			$response->responseMessage = "maximum object to process is ".MAX_SCOOP_IT_OBJECT;
			$response->responseData = NULL;
		}

	}

	processResponse($response);
}

/*
 * [1,2,3,4,....,n] */
function scoop_it_api_delete()
{
	addResponseHeader();

	$response = new DnaMainResponse();
	DnaMainUtility::verifyOauthRequest();
	$dataObjectParam = isset($_POST["node_ids"])?$_POST["node_ids"]:NULL;
	$dataObjects = json_decode($dataObjectParam);

	if($dataObjects != NULL || $dataObjectParam==='n' || $dataObjectParam==='[n]')
	{

		if(!is_array($dataObjects) && !($dataObjectParam==='n' || $dataObjectParam==='[n]')){
			$dataObjects = array($dataObjects);
		}
		else if($dataObjectParam==='n' || $dataObjectParam==='[n]')
		{
			$dataObjects = array('n');
		}

		$object_count = sizeof($dataObjects);

		if($object_count<=MAX_SCOOP_IT_OBJECT && $object_count>0){

			$dataMappingObject = DnaContentMapService::getInstance();

			//delete the node objects
			$nodeObjectsResponse = $dataMappingObject->deleteNodes($dataObjects);
			$processedObjects = $nodeObjectsResponse;

			$response->responseCode = "0";
			$contents = DnaMiscUtility::getAssociatedArrayKeyDataFromObject($processedObjects);
			$response->responseMessage = "success";
			$response->responseData = $contents;
		}
		else
		{

			$response->responseCode = "-1";
			$response->responseMessage = "maximum object to process is ".MAX_SCOOP_IT_OBJECT;
			$response->responseData = NULL;
		}

	}

	processResponse($response);
}


/*
 * Image Request scoop_it_data:
{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0}
Single Post Request scoop_it_data:
{"id":1,"publicationDate":"d-m-Y H:i:s","title":"Title for post 1","summary":"This is summary for post 1","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":["tag1","tag2","tag3"],"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 1 content"}
Multiple Post Request scoop_it_data:
[{"id":1,"publicationDate":"d-m-Y H:i:s","title":"Title for post 1","summary":"This is summary for post 1","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":["tag1","tag2","tag3"],"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 1 content"},{"id":2,"publicationDate":"d-m-Y H:i:s","title":"Title for post 2","summary":"This is summary for post 2","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":null,"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 2 content"},{"id":3,"publicationDate":"d-m-Y H:i:s","title":"Title for post 3","summary":"This is summary for post 3","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":null,"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 3 content"},{"id":4,"publicationDate":"d-m-Y H:i:s","title":"Title for post 4","summary":"This is summary for post 4","state":"published","image":{"id":1,"url":"http:\/\/url.to.image","scoopit_type":"Image","local_object_id":0},"tags":null,"scoopit_type":"Post","local_type":"article","local_object_id":0,"content":"this is post 4 content"}]
 */
function scoop_it_api_update()
{
	addResponseHeader();

	$response = new DnaMainResponse();
	DnaMainUtility::verifyOauthRequest();
	$dataObjectParam = isset($_POST["scoop_it_data"])?$_POST["scoop_it_data"]:NULL;
	$dataObjects = json_decode($dataObjectParam);

	if($dataObjects != NULL )
	{

		if(!is_array($dataObjects)){
			$dataObjects = array($dataObjects);
		}

		$object_count = sizeof($dataObjects);

		if($object_count<=MAX_SCOOP_IT_OBJECT && $object_count>0){

			$dataMappingObject = DnaContentMapService::getInstance();
			$processedObjects = array();

			$localObjectArguments = array();

			//create the node object arguments
			foreach ($dataObjects as $remoteObject)
			{
				$nodeCreationArgument = $dataMappingObject->mapRemoteObjectToLocalArgumentForAction($remoteObject);
				$localObjectArguments[] = $nodeCreationArgument;
			}

			//create the node objects
			$nodeObjectsResponse = $dataMappingObject->updateNodes($localObjectArguments);

			//convert the node objects to remote objects
			$i=0;
			foreach ($nodeObjectsResponse as $nodeObject)
			{
				$retObj = $dataMappingObject->mapLocalObjectToRemoteObject($nodeObject,$dataObjects[$i]->scoopit_type);
				if($retObj)
					$processedObjects[] = $retObj;
				$i++;
			}

			$response->responseCode = "0";
			$contents = DnaMiscUtility::getAssociatedArrayKeyDataFromObject($processedObjects);
			$response->responseMessage = "success";
			$response->responseData = $contents;
		}
		else
		{

			$response->responseCode = "-1";
			$response->responseMessage = "maximum object to process is ".MAX_SCOOP_IT_OBJECT;
			$response->responseData = NULL;
		}

	}

	processResponse($response);
}

/*
 * Authors Request scoop_it_data:
 */
function scoop_it_api_get_authors()
{
	addResponseHeader();
	DnaMainUtility::verifyOauthRequest();

	$response = new DnaMainResponse();


	$author_role = variable_get('scoopit_author_role', '');

	$author_id = variable_get('scoopit_author_id', '');


	// display current scoppit users authors
	$result = entity_load('user');
	// create table
	//$header = array('Id', 'Username', /*'Role',*/ 'Action');
	$rows = array();
	// Looping for filling the table rows
	foreach($result as $data){
		// Fill the table rows

		if(in_array($author_role,$data->roles)){
			$rows[] = array(
				'id'=>$data->uid,
				'username'=>$data->name,
				//$data->roleid,
				//'<input type="radio" name="scoopItUserSel" value="'.$data->uid.'" '.(($author_id==$data->uid)?' checked="checked" ':' ').' onclick="saveScoopitAuthor(this);" />',
			);
		}

	}

	$response->responseCode = "0";
	$contents = DnaMiscUtility::getAssociatedArrayKeyDataFromObject($rows);
	$response->responseMessage = "success";
	$response->responseData = $contents;

	processResponse($response);
}


/*
 * node_ids=> [1,2,3,4,....,n]
 * content_type => string local type e.g. article
 *  */
//request for content list either by their ids or by their type of which type takes preference over ids e.g. type => Post
function scoop_it_api_list()
{
	addResponseHeader();

	DnaMainUtility::verifyOauthRequest();
	$response = new DnaMainResponse();
	//$type = isset($_POST["content_type"])?$_POST["content_type"]:NULL;
	$nodeIds = isset($_POST["node_ids"])?$_POST["node_ids"]:NULL;

	if(/*$type ||*/ trim($nodeIds)!='')
	{
		$dataMappingObject = DnaContentMapService::getInstance();

		$nodeObjectsResponse = NULL;
		/*if($type)
		{
			$nodeObjectsResponse = $dataMappingObject->getNodesByType($type);
		}
		else */if($nodeIds)
		{

			if($nodeIds!=='n')
			{
				$nodeIds = (array)json_decode($nodeIds);
			}

			if(!is_array($nodeIds)){
				$nodeIds = array($nodeIds);
			}
			$nodeObjectsResponse = $dataMappingObject->getNodes($nodeIds);
		}

		//process content pull into the scoop it type before returning it
		if($nodeObjectsResponse && is_array($nodeObjectsResponse) && !empty($nodeObjectsResponse))
		{
			$processedObjects = array();
			//convert the node objects to remote objects
			$i=0;
			foreach ($nodeObjectsResponse as $nodeObject)
			{
				$scoop_it_type = $dataMappingObject->getRemoteTypeFromLocalObject($nodeObject);
				$retObj = $dataMappingObject->mapLocalObjectToRemoteObject($nodeObject,$scoop_it_type);
				if($retObj)
					$processedObjects[] = $retObj;
				$i++;
			}

			$contents = DnaMiscUtility::getAssociatedArrayKeyDataFromObject($processedObjects);
			$response->responseData = $contents;
			$response->responseMessage = "success";
			$response->responseCode = "0";
		}
		else
		{
			$response->responseData = NULL;
			$response->responseMessage = "no record found";
			$response->responseCode = "1";
		}
	}
	else
	{
		$response->responseCode = "-1";
		$response->responseMessage = "No type or node ids set. kindly set any of the fields content_type or node_ids.";
		$response->responseData = NULL;
	}

	processResponse($response);
}

/*
 * node_id=> integer
 *  */
function scoop_it_api_get()
{
	DnaMainUtility::verifyOauthRequest();
	$response = new DnaMainResponse();
	$nodeId = (int)isset($_POST["node_id"])?$_POST["node_id"]:0;

	if($nodeId>0)
	{
		$dataMappingObject = DnaContentMapService::getInstance();

		$nodeObjectResponse = $dataMappingObject->getNode($nodeId);

		//process content pull into the scoop it type before returning it
		if($nodeObjectResponse)
		{
			//convert the node object to remote object
			$scoop_it_type = $dataMappingObject->getRemoteTypeFromLocalObject($nodeObjectResponse);
			$processedObject = $dataMappingObject->mapLocalObjectToRemoteObject($nodeObjectResponse,$scoop_it_type);

			$content = DnaMiscUtility::getAssociatedArrayKeyDataFromObject($processedObject);
			$response->responseData = $content;
			$response->responseMessage = "success";
			$response->responseCode = "0";
		}
		else
		{
			$response->responseData = NULL;
			$response->responseMessage = "no record found";
			$response->responseCode = "1";
		}
	}
	else
	{
		$response->responseCode = "-1";
		$response->responseMessage = "No node id set. kindly set the field node_id.";
		$response->responseData = NULL;
	}

	processResponse($response);
}

function scoop_it_api_request_token()
{
	$oAuthObject = DnaMainUtility::getOauth();

	// Handle a request for an OAuth2.0 Access Token and send the response to the client
	$oAuthObject->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

	addResponseHeader();
}

function scoop_it_api_authorize()
{
	$oAuthObject = DnaMainUtility::getOauth();

	$request = OAuth2\Request::createFromGlobals();
	$response = new OAuth2\Response();

// validate the authorize request
	if (!$oAuthObject->validateAuthorizeRequest($request, $response)) {
		$response->send();
		die;
	}

// print the authorization code if the user has authorized your client
	$is_authorized = true;//
	$oAuthObject->handleAuthorizeRequest($request, $response, $is_authorized);
	/*if ($is_authorized) {
		// this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
		$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
		exit("SUCCESS! Authorization Code: $code");
	}

	$response->send();*/

	$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
	$response = new DnaMainResponse();
	$response->responseCode = "0";
	$response->responseMessage = "success";
	$response->responseData = $code;
	processResponse($response);
}

function addResponseHeader()
{

	header("Access-Control-Request-Headers: X-Requested-With, accept, content-type");
	header("Access-Control-Allow-Methods: GET, POST");
	header("Access-Control-Allow-Origin: *");
}

function processResponse($response)
{
	$response->version = SCOOPIT_API_VERSION;

	addResponseHeader();
	$result = str_replace('\u0000','',str_replace('\u0000*\u0000','',json_encode($response)));
	echo $result;
	die();

}

function scoop_it_api_get_remote_content_type()
{
	$content_type = isset($_GET["content-type"])?$_GET["content-type"]:"";
	$local_type = isset($_GET["local-type"])?$_GET["local-type"]:"";
	$aFieldHasSummary = false;

	$remote_entities = DnaMiscUtility::getFilesInFolder(dirname(__FILE__).'/dna.libraries/Scoopit/Entities',false,'.php');

	$picked_type = NULL;
	foreach ($remote_entities as $entity)
	{
		$entity_name = basename($entity,'.php');

		if($content_type==$entity_name){
			$picked_type = $entity_name;
			break;
		}
	}

	$returnVal = "<option>--- SELECT ---</option>";

	if($content_type!="" && $picked_type){

		$fieldInfos = field_info_instances("node", $local_type);
		if(is_array($fieldInfos) && !empty($fieldInfos)){
			foreach ($fieldInfos as $fieldInfo) {
				//...
				if(trim($fieldInfo['widget']['type'])=='text_textarea_with_summary') {
					$aFieldHasSummary = true;
					break;
				}
			}
		}

		$dataMappingObject = DnaContentMapService::getInstance();
		$alreadyMappedFields = $dataMappingObject->getMaps($local_type,$content_type);

		$skippedFields = array("scoopit_type","local_object_id","local_type","id","state","title","publicationDate","url","author",);

		if($aFieldHasSummary)
		{
			$skippedFields[] = "summary";
		}

		if(is_array($alreadyMappedFields) && !empty($alreadyMappedFields))
		{
			foreach ($alreadyMappedFields as $mappedField) {
				$skippedFields[] = $mappedField->remote_field_name;
			}
		}

		$className = "Scoopit\\Entities\\".$picked_type;
		$classObj = new $className();
		$fields = get_object_vars($classObj);
		if(sizeof($fields)>0)
		{
			foreach ($fields as $field=>$temp_val) {
				//...
				if(in_array($field,$skippedFields)) continue;
				$returnVal .= "<option value='{$field}'>" . preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", " ", ucfirst($field)) . "</option>";
			}
			//$returnVal .= "<option value='scoopit_content_type_mapper'>" . "Scoop it content type Mapper (Not Required)" . "</option>";
		}

	}

	die($returnVal);
}

function scoop_it_api_get_local_content_type()
{
	$content_type = isset($_GET["content-type"])?$_GET["content-type"]:"";

	$local_types = node_type_get_types();
	$picked_type = NULL;
	foreach ($local_types as $type){
		if($content_type==$type->type){
			$picked_type = $type;
			break;
		}
	}

	$returnVal = "<option>--- SELECT ---</option>";

	if($content_type!="" && $picked_type) {
		$fields = field_info_instances("node", $picked_type->type);

		$dataMappingObject = DnaContentMapService::getInstance();
		$alreadyMappedFields = $dataMappingObject->getMapsByLocalContentType($content_type);

		$skippedFields = array("field_scoopit_id",);

		if(is_array($alreadyMappedFields) && !empty($alreadyMappedFields))
		{
			foreach ($alreadyMappedFields as $mappedField) {
				$skippedFields[] = $mappedField->local_field_name;
			}
		}

		if(sizeof($fields)>0){
			foreach ($fields as $field) {
				//...
				if(in_array($field['field_name'],$skippedFields)) continue;
					$returnVal .= "<option value='{$field['field_name']}'>" . $field['label']." (".($field['required']?"Required":"Not Required").")" . "</option>";
			}
			//$returnVal .= "<option value='drupal_content_type_mapper'>" . "Drupal content type Mapper  (Not Required)" . "</option>";
		}
	}

	die($returnVal);
}

function scoop_it_check_local_content_type_for_scoopit_id_field($content_type)
{

	$local_types = node_type_get_types();
	$picked_type = NULL;
	foreach ($local_types as $type){
		if($content_type==$type->type){
			$picked_type = $type;
			break;
		}
	}

	$returnVal = false;

	if($content_type!="" && $picked_type) {
		$fields = field_info_instances("node", $picked_type->type);

		foreach ($fields as $field) {
			//...
			if($field['field_name']=="field_scoopit_id"){
				$returnVal = true; break;
			}
		}
	}

	return ($returnVal);
}

function scoop_it_create_scoopit_id_field_for_local_content_type($content_type)
{
	$field_name = 'field_scoopit_id';
	// Make sure the field doesn't already exist.
	if (!field_info_field($field_name)) {
		// Create the field.
		$field = array(
			'field_name' => $field_name,
			'type' => 'number_integer',
			//text/*list_boolean=>Boolean,number_decimal=>Decimal,file=>File,number_float=>Float,image=>Image,number_integer=>Integer,list_float=>List (float),list_integer=>List (integer),list_text=>List (text),text_long=>Long text,text_with_summary=>Long text and summary,taxonomy_term_reference=>Term reference,text=>Text
			//*/
			'settings' => array('max_length' => 20),
		);
		field_create_field($field);
	}

	// Create the instance.
	$instance = array( 'field_name' => $field_name,
		'entity_type' => 'node',
		'bundle' => $content_type,//'article',
		'label' => 'Scoopit Id',
		'description' => 'The '.$content_type.' scoopit id.',
		'required' => false,
	);
	field_create_instance($instance);

	watchdog('scoopit', t('!field_name was added to '.$content_type.' successfully.', array('!field_name' => $field_name)));
}
