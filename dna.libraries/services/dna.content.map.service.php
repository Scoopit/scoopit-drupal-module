<?php

/**
 * Created by PhpStorm.
 * User: dauduadetokunbo
 * Date: 02/08/2016
 * Time: 16:48
 */
class DnaContentMapService extends DnaMainService
{
	private static $instance = NULL;

	public static function getInstance()
	{
		if (self::$instance == NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct()
	{
		parent::__construct();
	}

	public function getMaps($localContentType, $remoteContentType)
	{
		/*
		 *

		$sql = "SELECT * FROM scoopit_api_content_map WHERE local_type ='{$localContentType}' AND remote_type ='{$remoteContentType}' ";
		$result = db_query($sql);

		$rows = array();
		// Looping for filling the records to be returned
		while($data = $result->fetchObject()){
			$rows[] = $data;
		}

		$result = db_select('scoopit_api_content_map', 's')
			->fields('s')
			->condition('local_type', $localContentType, '=')
			->condition('remote_type', $remoteContentType, '=')
			->execute()
			->fetchAllAssoc('Seq');

		*/

		$stmt = db_query("SELECT * FROM {scoopit_api_content_map} WHERE local_type = :local_type and remote_type = :remote_type",
			array(':local_type' => $localContentType,
				':remote_type' => $remoteContentType,
			)
		);
		//$data = $stmt->fetchAllAssoc('Seq', PDO::FETCH_OBJ);
		$data = $stmt->fetchAll();

		//$stmt->fetchAllAssoc('Seq', PDO::FETCH_ASSOC);

		return $data;
	}

	public function getMapsByLocalContentType($localContentType)
	{

		//$stmt = db_query("SELECT * FROM {scoopit_api_content_map} WHERE local_type = :local_type and local_field_name = :local_field_name;",
		$stmt = db_query("SELECT * FROM {scoopit_api_content_map} WHERE local_type = :local_type ;",
			array(
				':local_type' => $localContentType,
				//':local_field_name' => 'drupal_content_type_mapper',
			)
		);
		//$data = $stmt->fetchAllAssoc('Seq', PDO::FETCH_OBJ);
		$data = $stmt->fetchAll();

		//$stmt->fetchAllAssoc('Seq', PDO::FETCH_ASSOC);

		return $data;
	}

	public function getMapsByRemoteContentType($remoteContentType)
	{

		//$stmt = db_query("SELECT * FROM {scoopit_api_content_map} WHERE remote_type = :remote_type and remote_field_name = :remote_field_name;",
		$stmt = db_query("SELECT * FROM {scoopit_api_content_map} WHERE remote_type = :remote_type ;",
			array(
				':remote_type' => $remoteContentType,
				//':remote_field_name' => 'scoopit_content_type_mapper',
			)
		);
		//$data = $stmt->fetchAllAssoc('Seq', PDO::FETCH_OBJ);
		$data = $stmt->fetchAll();

		//$stmt->fetchAllAssoc('Seq', PDO::FETCH_ASSOC);

		return $data;
	}

	public function getLocalTypeFromRemoteObject($dataObject)
	{
		$remoteContentType = $dataObject->scoopit_type;
		$localObjectType = NULL;
		//get the first object local type you find bound to this type of remote object
		$tempMaps = $this->getMapsByRemoteContentType($remoteContentType);
		if (is_array($tempMaps) && !empty($tempMaps)) {
			foreach ($tempMaps as $tempMap) {
				$localObjectType = $tempMap->local_type;
				break;
			}

		}

		return $localObjectType;
	}

	public function getRemoteTypeFromLocalObject($dataNode)
	{
		$localObjectType = $dataNode->type;
		$remoteContentType = NULL;
		//get the first object remote type you find bound to this type of local object
		$tempMaps = $this->getMapsByLocalContentType($localObjectType);
		if (is_array($tempMaps) && !empty($tempMaps)) {
			foreach ($tempMaps as $tempMap) {
				$remoteContentType = $tempMap->remote_type;
				break;
			}

		}

		return $remoteContentType;
	}

	//$dataMap a local object to remote object
	public function mapLocalObjectToRemoteObject($dataNode, $remoteObjectClass)
	{
		$localContentType = $dataNode->type;

		$fieldInfos = field_info_instances("node", $localContentType);

		$mappingObjects = $this->getMaps($localContentType, $remoteObjectClass);
		$retObj = NULL;

		if (is_array($mappingObjects) && !empty($mappingObjects)) {
			$remoteObjectClassName = "Scoopit\\Entities\\" . $remoteObjectClass;
			$remoteObject = new $remoteObjectClassName();

			foreach ($mappingObjects as $mappingObject) {
				$remote_field_name = $mappingObject->remote_field_name;
				$local_field_name = $mappingObject->local_field_name;

				if ($local_field_name == 'drupal_content_type_mapper') {
					continue;
				}

				$fieldInfo = NULL;

				foreach ($fieldInfos as $fieldInfo) {
					//...
					if ($fieldInfo['field_name'] == $local_field_name)
						break;
				}

				$nodeField = $dataNode->$local_field_name;

				//if($local_field_name=='field_image')
				if (trim($fieldInfo['widget']['type']) == 'image_image' || trim($fieldInfo['widget']['module']) == 'image') {
					$remoteObjectImageClassName = "Scoopit\\Entities\\Sub\\Image";
					$remoteImageObject = new $remoteObjectImageClassName();
					$tmpImages = $nodeField[/*$node->language*/
					$this->pick_field_language($dataNode->language, 'image')];//(isset($nodeField[$dataNode->language])?$nodeField[$dataNode->language]:$nodeField['und'])
					foreach ($tmpImages as $tempImage) {
						$img_url = $tempImage['uri'];
						break;
					}
					$wrapper = file_stream_wrapper_get_instance_by_uri('public://');
					$doc_root = $_SERVER["DOCUMENT_ROOT"];
					$realpath = str_replace($doc_root, '', $wrapper->realpath());
					///Users/dauduadetokunbo/Sites/projects/php/web/drupal/scoopit drupal project/SRC/sites/default/files/thumb?content-type=17&content-id=5&fname=thumb.jpg
					///Users/dauduadetokunbo/Sites/projects/php/web/drupal/scoopit drupal project/SRC/sites/default/files/styles/large/public/champions_0_0.png
					$remoteImageObject->url = (SCOOPIT_API_LOCAL_SERVER) . str_replace('public:/', $realpath, $img_url);//site url to the image

					$remoteImageObject->url = SCOOPIT_API_SERVER_SCHEME . str_replace(array('///', '//'), '/', $remoteImageObject->url);
					$tmpImages = $nodeField[/*$node->language*/
					$this->pick_field_language($dataNode->language, 'image')];//(isset($nodeField[$dataNode->language])?$nodeField[$dataNode->language]:$nodeField['und'])
					foreach ($tmpImages as $tempFid) {
						$fid = $tempFid['fid'];
						$remoteImageObject->id = $fid;//may not be necessary
						break;
					}

					//$remoteImageObject->local_type = 'image';//may not be necessary
					unset($remoteImageObject->scoopit_type);
					if ($remoteImageObject->id != NULL) {
						$remoteObject->$remote_field_name = $remoteImageObject;
					} else {
						$remoteObject->$remote_field_name = NULL;
					}
					continue;
				} //else if($local_field_name=='field_tags')
				else if (trim($fieldInfo['widget']['type']) == 'taxonomy_autocomplete' || trim($fieldInfo['widget']['module']) == 'taxonomy') {
					$t = $dataNode->$local_field_name;
					$tags = $t[/*$node->language*/
					$this->pick_field_language($dataNode->language, 'taxonomy')];//isset($t[$dataNode->language])?$t[$dataNode->language]:$t[LANGUAGE_NONE];
					$tagsRet = array();
					foreach ($tags as $tag) {
						$taxonomy = taxonomy_term_load($tag['tid']);
						$tagsRet[] = $taxonomy->name;
					}

					$remoteObject->$remote_field_name = $tagsRet;//explode(',',$dataNode->$local_field_name);
					continue;
				}

				$contentField = $dataNode->$local_field_name;

				/*if(isset($dataNode->body)){

					foreach((isset($dataNode->body[$dataNode->language])?$dataNode->body[$dataNode->language]:$dataNode->body['und']) as $tempBody)
					{
						$body = $tempBody['value'];
						$bodySummary = $tempBody['summary'];
						break;
					}
					$remoteObject->content = $body;
					$remoteObject->summary = $bodySummary;
				}*/
				//if(isset($contentField[$dataNode->language][0]['value'])||isset($contentField[LANGUAGE_NONE][0]['value']))
				if (isset($contentField[/*$node->language*/$this->pick_field_language($dataNode->language,'body')][0]['value']))
				{

					$tmpContent = $contentField[/*$node->language*/$this->pick_field_language($dataNode->language,'body')];//(isset($contentField[$dataNode->language]) ? $contentField[$dataNode->language] : $contentField[LANGUAGE_NONE])
					foreach ($tmpContent as $tempBody) {
						$body = $tempBody['value'];
						$remoteObject->$remote_field_name = $body;
						$bodySummary = $tempBody['summary'];
						$remoteObject->summary = $bodySummary;

						break;
					}

				} else {
					$remoteObject->$remote_field_name = $dataNode->$local_field_name;
				}

			}

			$remoteObject->local_object_id = $dataNode->nid;


			if (isset($dataNode->title))
				$remoteObject->title = $dataNode->title;

			$lang_default = language_default();

			$remoteObject->local_type = $localContentType;
			$remoteObject->state = ($dataNode->status == 1) ? 'published' : 'scheduled';
			$remoteObject->scoopit_language = $dataNode->language;
			$remoteObject->id = $dataNode->field_scoopit_id[$dataNode->language][0]['value'];//force users to create this field
			$remoteObject->publicationDate = date('Y-m-d H:i:s O', $dataNode->created);

			//if(isset($remoteObject->url))
			$remoteObject->url = SCOOPIT_API_LOCAL_SERVER . '/node/' . $dataNode->nid;
			$remoteObject->url = SCOOPIT_API_SERVER_SCHEME . str_replace(array('///', '//'), '/', $remoteObject->url);


			//removing unwanted fields from the scoopit
			if (isset($remoteObject->scoopit_type))
				unset($remoteObject->scoopit_type);

			if (isset($remoteObject->local_type))
				unset($remoteObject->local_type);

			if (isset($remoteObject->local_object_id))
				unset($remoteObject->local_object_id);


			$remoteObject->id = $dataNode->nid;//force node to default to local field id

			$user = user_load($dataNode->uid);
			$username = $user->name;

			$remoteObject->author = $username;//force node to pick username for author

			$retObj = $remoteObject;
		} else {
			$retObj = NULL;
		}

		return $retObj;
	}

	//$dataMap a remote object to local object for action
	public function mapRemoteObjectToLocalArgumentForAction($dataObject)
	{
		$dataObject->scoopit_type = "Post";
		$remoteContentType = $dataObject->scoopit_type;
		$localObjectType = ($dataObject->local_type && trim($dataObject->local_type) != '') ? $dataObject->local_type : $this->getLocalTypeFromRemoteObject($dataObject);
		$localObjectId = (int)"0" . $dataObject->id;

		if ($localObjectType == NULL) {
			return NULL;
		}

		$mappingObjects = $this->getMaps($localObjectType, $remoteContentType);
		$retObj = NULL;

		if (is_array($mappingObjects) && !empty($mappingObjects)) {
			$nodeData = new \stdClass();
			$nodeFieldValues = array();
			foreach ($mappingObjects as $mappingObject) {
				$remote_field_name = $mappingObject->remote_field_name;
				$local_field_name = $mappingObject->local_field_name;
				/*if($local_field_name=='field_tags')
				{
					$remoteObject->$remote_field_name = explode(',',$dataNode->$local_field_name);
					continue;
				}*/

				$nodeFieldValues[$local_field_name] = $dataObject->$remote_field_name;
			}

			$nodeFieldValues['field_scoopit_id'] = $dataObject->id;

			if (isset($dataObject->publicationDate))
				$nodeFieldValues['publicationDate'] = $dataObject->publicationDate;

			if (isset($dataObject->summary))
				$nodeFieldValues['scoopit_summary'] = $dataObject->summary;

			//$nodeData->node_body_content = $dataObject->content;

			if (isset($dataObject->title))
				$nodeData->node_title = $dataObject->title;

			//if(isset($dataObject->tags))
			//$nodeFieldValues['field_tags'] = $dataObject->tags;

			$nodeData->node_content_type = $localObjectType;
			$nodeData->node_field_values = $nodeFieldValues;
			//$nodeData->term_reference_id = "";//to be determined later
			//$nodeData->entity_id = "";//to be determined later
			$nodeData->promoted_to_front_page = "1";//to be determined later
			$nodeData->comment_disabled = "0";//to be determined later
			$nodeData->local_object_id = $localObjectId;//to be determined later
			$nodeData->state = $dataObject->state;//(==1)?'published':'scheduled';
			$nodeData->scoopit_language = $dataObject->scoopit_language;//language;

			$retObj = $nodeData;
		} else {
			$retObj = NULL;
		}

		return $retObj;
	}

}
