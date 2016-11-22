<?php

/**
 * Created by PhpStorm.
 * User: dauduadetokunbo
 * Date: 02/08/2016
 * Time: 16:48.
 */
class DnaMainService
{
    private static $instance = null;
    protected $utilObj = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->utilObj = new DnaMainUtility();
    }

    public function createNode($nodeContentType, $nodeFieldValues, $nodeTitle, $status, $promoted_to_front_page = 1, $comment_disabled = 0)
    {
        //global $user;
        //needs checking
        global $language;
        $lang_name = LANGUAGE_NONE; //$language->language;
        $setSummaryValue = false;
        $setContentFieldName = false;

        $fieldInfos = field_info_instances('node', $nodeContentType);

        $node = new \stdClass();
        $imagePresent = false;
        $imageField = '';

        $node->language = ($lang_name != null && trim($lang_name) != '') ? $lang_name : LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled

        if ($nodeFieldValues && is_array($nodeFieldValues) && !empty($nodeFieldValues)) {
            //$node->title = $title;//"YOUR TITLE";
            foreach ($nodeFieldValues as $field => $fieldValue) {
                if ($field == 'drupal_content_type_mapper') {
                    continue;
                }
                $fieldInfo = null;

                foreach ($fieldInfos as $fieldInfo) {
                    //...
                        if ($fieldInfo['field_name'] == $field) {
                            break;
                        }
                }

                // = $node->$field;

                /*if($field=='field_image')
                {
                $this->addImageToNode($fieldValue->url,$node);
                continue;
                }*/
                if (trim($fieldInfo['widget']['type']) == 'image_image' || trim($fieldInfo['widget']['module']) == 'image') {
                    if ($this->addImageToNode($fieldValue->url, $node, $field)) {
                        $imagePresent = true;
                    } else {
                        $imageField = $field;
                    }
                    continue;
                } else {
                    if (trim($fieldInfo['widget']['type']) == 'taxonomy_autocomplete' || trim($fieldInfo['widget']['module']) == 'taxonomy') {
                        $nodeFieldValue = array();
                        $tagIds = $this->processTags($fieldValue);
                        if (!empty($tagIds)) {
                            for ($i = 0; $i < sizeof($tagIds); ++$i) {
                                $tid = $tagIds[$i];
                                if ($tid != null) {
                                    $nodeFieldValue[$node->language][$i]['tid'] = $tid; //implode(',',$fieldValue);
                                }
                            }
                        }
                        $node->$field = $nodeFieldValue;
                        continue;
                    }
                    /*else if($field=='field_tags')
                    {
                    $tagIds = $this->processTags($fieldValue);
                    if(!empty($tagIds)){
                    for($i=0;$i<sizeof($tagIds);$i++)
                    {
                    $tid =$tagIds[$i];
                    if($tid!=NULL)
                    $node->field_tags[$node->language][$i]['tid'] = $tid; //implode(',',$fieldValue);
                    }
                    }
                    continue;
                    }

                    else if($field=='field_scoopit_id')
                    {
                    $node->field_scoopit_id[$node->language][0]['value'] = $fieldValue;
                    continue;
                    }*/
                    else {
                        if ($field == 'scoopit_summary') {
                            $setSummaryValue = $fieldValue;
                            if ($setContentFieldName) {
                                $nodeFieldValue = $node->$setContentFieldName;
                                $nodeFieldValue[$node->language][0]['summary'] = $setSummaryValue;
                                $node->$setContentFieldName = $nodeFieldValue;
                            }
                            continue;
                        }
                    }
                }

                /*if(($nodeBodyContent))
                {
                $node->body[$node->language][0]['value'] = $fieldValue;
                $node->body[$node->language][0]['summary'] = $nodeFieldValues['summary'];
                }*/

                if (trim($fieldInfo['widget']['type']) == 'text_textarea_with_summary') {
                    $nodeFieldValue = array();
                    $setContentFieldName = $field;
                    $nodeFieldValue[$node->language][0]['value'] = $fieldValue;
                    $nodeFieldValue[$node->language][0]['format'] = 'full_html';
                    if ($setSummaryValue) {
                        $nodeFieldValue[$node->language][0]['summary'] = $setSummaryValue;
                    }
                    $node->$field = $nodeFieldValue;
                } else {
                    $node->$field = $fieldValue;
                }
            }
        } else {
            return null; //node does not have fields set
        }

        $node->type = $nodeContentType; //"YOUR_NODE_TYPE";

        if (($nodeTitle)) {
            $node->title = $nodeTitle;
        }

        node_object_prepare($node); // Sets some defaults. Invokes hook_prepare() and hook_node_prepare().

        $author_id = variable_get('scoopit_author_id', '');

        $node->uid = ($author_id) ? $author_id : 1; //$user->uid;
        $node->status = (trim($status) == 'published') ? 1 : 0; //(1 or 0): published or not
        $node->promote = $promoted_to_front_page; //(1 or 0): promoted to front page
        $node->comment = $comment_disabled; // 0 = comments disabled, 1 = read only, 2 = read/write

        $node->format = 3; //set format to full html // 1 means filtered html, 2 means full html, 3 is php

        if (isset($nodeFieldValues['publicationDate'])) {
            $createdTime = strtotime($nodeFieldValues['publicationDate']);
            if ($createdTime > 0) {
                $node->date = $nodeFieldValues['publicationDate'];
                $node->created = $createdTime;
            }
        }

        // Term reference (taxonomy) field - not sure we need this
        //$node->field_product_tid[$node->language][]['tid'] = $term_reference_id;

        // Entity reference field - not sure we need this as well
        /*$node->field_customer_nid[$node->language][] = array(
        'target_id' => $entityId,
        'target_type' => 'node',
        );*/
        // 'node' is default,
        // Other possible values are "user" and"taxonomy_term"

        if (!$imagePresent && $imageField != '') {
            $node->$imageField = '';
        }

        $node = node_submit($node); // Prepare node for saving
        node_save($node);
        //drupal_set_message( "Node with nid " . $node->nid . " saved!\n");
        entity_get_controller('node')->resetCache(array($node->nid));

        return $node;
    }

    public function deleteNode($nodeId)
    {
        node_delete($nodeId);
    }

    public function updateNode($nodeId, $nodeFieldValues, $nodeTitle, $status)
    {
        $node = node_load($nodeId);

        $fieldInfos = field_info_instances('node', $node->type);

        $setSummaryValue = false;
        $setContentFieldName = false;
        $imagePresent = false;
        $imageField = '';

        if ($node && $nodeFieldValues && is_array($nodeFieldValues) && !empty($nodeFieldValues)) {
            /*
$node_wrapper = entity_metadata_wrapper('node', $node);

//$node->title = $title;//"YOUR TITLE";
foreach ($nodeFieldValues as $field=>$fieldValue)
{
$field_name_setter = 'field_'.$field;
$node_wrapper->$field_name_setter->set($fieldValue);
//$node_wrapper->field_myfield->set(1);
}

$node_wrapper->save();
*/

/*
db_update('mytable')
->fields(array('extra' => $node->extra))
->condition('nid', $node->nid)
->execute();
*/

foreach ($nodeFieldValues as $field => $fieldValue) {
    if ($field == 'drupal_content_type_mapper') {
        continue;
    }

    $fieldInfo = null;

    foreach ($fieldInfos as $fieldInfo) {
        //...
if ($fieldInfo['field_name'] == $field) {
            break;
        }
    }

// = $node->$field;

/*if($field=='field_image')
{
$this->addImageToNode($fieldValue->url,$node);
continue;
}*/
if (trim($fieldInfo['widget']['type']) == 'image_image' || trim($fieldInfo['widget']['module']) == 'image') {
    if ($this->addImageToNode($fieldValue->url, $node, $field)) {
        $imagePresent = true;
    } else {
        $imageField = $field;
    }

    continue;
} else {
    if (trim($fieldInfo['widget']['type']) == 'taxonomy_autocomplete' || trim($fieldInfo['widget']['module']) == 'taxonomy') {
        $nodeFieldValue = array();
        $tagIds = $this->processTags($fieldValue);
        if (!empty($tagIds)) {
            for ($i = 0; $i < sizeof($tagIds); ++$i) {
                $tid = $tagIds[$i];
                if ($tid != null) {
                    $nodeFieldValue[$node->language][$i]['tid'] = $tid; //implode(',',$fieldValue);
                }
            }
        }
        $node->$field = $nodeFieldValue;
        continue;
    }
/*else if($field=='field_tags')
{
$tagIds = $this->processTags($fieldValue);
if(!empty($tagIds)){
for($i=0;$i<sizeof($tagIds);$i++)
{
$tid =$tagIds[$i];
if($tid!=NULL)
$node->field_tags[$node->language][$i]['tid'] = $tid; //implode(',',$fieldValue);
}
}
continue;
}

else if($field=='field_scoopit_id')
{
$node->field_scoopit_id[$node->language][0]['value'] = $fieldValue;
continue;
}*/
else {
    if ($field == 'scoopit_summary') {
        $setSummaryValue = $fieldValue;
        if ($setContentFieldName) {
            $nodeFieldValue = $node->$setContentFieldName;
            $nodeFieldValue[$node->language][0]['summary'] = $setSummaryValue;
            $node->$setContentFieldName = $nodeFieldValue;
        }
        continue;
    }
}
}

/*if(($nodeBodyContent))
{
$node->body[$node->language][0]['value'] = $fieldValue;
$node->body[$node->language][0]['summary'] = $nodeFieldValues['summary'];
}*/

if (trim($fieldInfo['widget']['type']) == 'text_textarea_with_summary') {
    $nodeFieldValue = array();
    $setContentFieldName = $field;
    $nodeFieldValue[$node->language][0]['value'] = $fieldValue;
    $nodeFieldValue[$node->language][0]['format'] = 'full_html';
    if ($setSummaryValue) {
        $nodeFieldValue[$node->language][0]['summary'] = $setSummaryValue;
    }
    $node->$field = $nodeFieldValue;
} else {
    $node->$field = $fieldValue;
}
}

            if (($nodeTitle)) {
                $node->title = $nodeTitle;
            }

            $node->status = (trim($status) == 'published') ? 1 : 0; //(1 or 0): published or not

if (isset($nodeFieldValues['publicationDate'])) {
    $createdTime = strtotime($nodeFieldValues['publicationDate']);
    if ($createdTime > 0) {
        $node->date = $nodeFieldValues['publicationDate'];
        $node->created = $createdTime;
    }
}

            if (!$imagePresent && $imageField != '') {
                $node->$imageField = '';
            }

            node_save($node);

            entity_get_controller('node')->resetCache(array($node->nid));
            $retVal = $node;
        } else {
            $retVal = null;
        }

        return $retVal;
    }

    protected function processTags($arrTags)
    {
        $retTagIds = array();
        foreach ($arrTags as $arrTag) {
            //see if this term already exists and fetch it if it does
$term = taxonomy_get_term_by_name($arrTag);

//if it doesn't exist, make it
if ($term == array() || $term == null || empty($term)) {
    //make a new class to hold the term for terms 1
$terms = new stdClass();
    $terms->name = $arrTag;
    $term->description = $arrTag;
    $terms->vid = 1; //vocabulary is id for Tags
try {
        taxonomy_term_save($terms);
//now fetch it so we have it's tid
$term = taxonomy_get_term_by_name($arrTag);
//set $tid as it's returned fromtaxonomy_get_term_by_name
$tid = key($term);
    } catch (\Exception $e) {
        try {
            $tid = $this->saveRawTaxonomy($arrTag, $arrTag, 1);
        } catch (\Exception $ex) {
            $tid = null;
        }
    }
} else {
    //now fetch it so we have it's tid
$term = taxonomy_get_term_by_name($arrTag);
//set $tid as it's returned fromtaxonomy_get_term_by_name
$tid = key($term);
}

            $retTagIds[] = $tid;

//tag the node with the appropriate tag
//$node->field_tags['und'][0] = array('tid' => $tid);

//save the tagged node
//node_save($node);
        }

        return $retTagIds;
    }

    protected function saveRawTaxonomy($name, $description, $vid)
    {
        $record = array(
'vid' => $vid,
'name' => $name,
'description' => $description,
'format' => null,
'weight' => 0,
);
        $query = db_insert('taxonomy_term_data')->fields(array(
'vid',
'name',
'description',
'format',
'weight',
));
        $query->values($record);
        $id = $query->execute();

        return $id;
    }

    public function getNode($nodeId)
    {
        $node = node_load($nodeId);

        return $node;
    }

    public function createNodes($nodeVars)
    {
        $nodeUpdates = array();
        foreach ($nodeVars as $nodeData) {
            $nodeUpdates[] = $this->createNode($nodeData->node_content_type, $nodeData->node_field_values, $nodeData->node_title, $nodeData->state,
$nodeData->promoted_to_front_page, $nodeData->comment_disabled);
        }

        return $nodeUpdates;
    }

    public function getNodes($nodeIds)
    {
        $nodes = array();

        if (sizeof($nodeIds) === 1 && strtolower(trim($nodeIds[0])) == 'n') {
            $local_types = node_type_get_types();
            foreach ($local_types as $type) {
                $nodesTemp = $this->getNodesByType($type->type);
                $nodes = array_merge($nodes, $nodesTemp);
            }
        } else {
            foreach ($nodeIds as $nodeId) {
                $nodes[] = $this->getNode($nodeId);
            }
        }

        return $nodes;
    }

    public function getNodesByType($nodeType)
    {
        $nodes = node_load_multiple(array(), array('type' => $nodeType));

        return $nodes;
    }

    public function updateNodes($nodeVars)
    {
        $nodeIdUpdates = array();
        foreach ($nodeVars as $nodeArgObj) {
            $nodeIdUpdates[] = $this->updateNode($nodeArgObj->local_object_id, $nodeArgObj->node_field_values, $nodeArgObj->node_title, $nodeArgObj->state);
        }

        return $nodeIdUpdates;
    }

    public function deleteNodes($nodeIds)
    {
        $nodeIdUpdates = array();
        foreach ($nodeIds as $nodeId) {
            $this->deleteNode($nodeId);
            $nodeIdUpdates[] = $nodeId;
        }

        return $nodeIdUpdates;
    }

    public function addImageToNode($remote_url_path, &$node, $field)
    {
        $images_dir_path = DRUPAL_ROOT.base_path().
'images/scoopit/';

        if (!file_exists($images_dir_path)) {
            mkdir($images_dir_path, 0777, true);
        }

//$images_dir_path = 'images/scoopit/';

$image_file_name = basename($remote_url_path);

        try {
            if (function_exists('curl_version')) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $remote_url_path);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $file_content = curl_exec($curl);
//$content = curl_exec($curl);
curl_close($curl);
            } else {
                if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
                    //$content = file_get_contents($file);
$file_content = file_get_contents($remote_url_path);
                } else {
                    return false;
                }
            }
        } catch (Exception $ex) {
            $file_content = null;
        }

        if ($file_content != null) {
            $local_image_path = $images_dir_path.$image_file_name;

            file_put_contents($local_image_path, $file_content);

//$file = file_save_data($file_content, file_default_scheme().'://field/image/'.$image_file_name);
//$file->status = 1;
//$file->display = 1;
//$node->field_image[$node->language][0] = (array)$file;

//$file = file_save_data($file_content, $local_image_path, FILE_EXISTS_REPLACE);
//$node->field_image = array(LANGUAGE_NONE => array('0' => (array)$file));

//$filepath = drupal_realpath($local_image_path);
$filepath = $local_image_path;
// Create managed File object and associate with Image field.

$author_id = variable_get('scoopit_author_id', '');

            $file = (object) array(
'uid' => ($author_id) ? $author_id : 1,
'uri' => $filepath,
'filemime' => file_get_mimetype($filepath),
'status' => 1,
);

// We save the file to the root of the files directory.
$file = file_copy($file, 'public://', FILE_EXISTS_RENAME);

            $file = (array) $file;
            $file['status'] = 1;
            $file['display'] = 1;

//$node->field_image[$node->language][0] = $file;
$imageField = array();
            $imageField[$node->language][0] = $file;
            $node->$field = $imageField;

            $this->utilObj->deletefile($filepath);

            return true;
        } else {
            return false;
        }
    }
}