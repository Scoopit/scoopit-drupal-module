<?php
/**
 * Created by PhpStorm.
 * User: lecooper
 * Date: 01/08/2016
 * Time: 18:42
 */

/**
 * @file
 * Admin page callback for the sector limit module.
 */

/**
 * Builds and returns the sector limit settings form.
 */
function scoopit_admin_settings() {
	$returnObj = array(
		'#markup' => "<div><p><a href='".SCOOPIT_API_SERVER_SCHEME.str_replace(array('///','//'),'/',SCOOPIT_API_LOCAL_SERVER."/admin")."/config/scoopit/scoopit-api-manager'>Api Settings</a></p></div>
			<div><p><a href='".SCOOPIT_API_SERVER_SCHEME.str_replace(array('///','//'),'/',SCOOPIT_API_LOCAL_SERVER."/admin")."/config/scoopit/scoopit-mapping-manager'>Mapping Settings</a></p></div>"
	);
	return $returnObj;
}

function scoopit_api_settings() {

	/*
	$result = db_select('node', 'n')
		->fields('n')
		->condition('nid', $node->nid,'=')
		->condition('status', 0,'>')
		->condition('uid', array(1,5,7),'IN')
		->execute()
		->fetchAssoc();
*/

	$result = db_select('scoopit_api_users', 'c')
		->fields('c')
		->execute()
		->fetchAssoc();

	$form['scoopit_email'] = array(
		'#type' => 'textfield',
		'#title' => t('Email'),
		'#default_value' => (($result)?$result['email']:''),
		'#description' => t('Enter your email.'),
		'#required' => TRUE,
	);
	$form['scoopit_customer_key'] = array(
		'#type' => 'textfield',
		'#title' => t('Customer Key'),
		'#default_value' => (($result)?$result['consumer_key']:'please generate using a valid email address from scoopit'),
		'#description' => t('Copy the value here for .'),
		'#attributes' => array('readonly' => 'readonly')
	);
	$form['scoopit_customer_secret'] = array(
		'#type' => 'textfield',
		'#title' => t('Customer Secret'),
		'#default_value' => (($result)?$result['consumer_secret']:'please generate using a valid email address from scoopit'),
		'#description' => t('Copy the value here for .'),
		'#attributes' => array('readonly' => 'readonly')
	);

	return system_settings_form($form);
}

function scoopit_mapping_settings() {

	if(isset($_GET["del_id"]))
	{
		$del_id = $_GET["del_id"];

		db_delete('scoopit_api_content_map')
			->condition('id', $del_id, '=')
			->execute();

		header('Location: '.SCOOPIT_API_SERVER_SCHEME.str_replace(array('///','//'),'/',SCOOPIT_API_LOCAL_SERVER."/admin").'/config/scoopit/scoopit-mapping-manager');
	}

	$extra_inclusion = "";
	if (function_exists('curl_version'))
	{
	}
	else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen'))
	{
	}
	else
	{
		$extra_inclusion = 'You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!';
	}

	/*$extra_inclusion = "Sample Request scoop_it_data: ";
	$extra_inclusion .= "<br/>";
	$extra_inclusion .= "Single Image Request scoop_it_data: ";
	$extra_inclusion .= "<br/>";

	$image = new Scoopit\Entities\Sub\Image();
	$image->url = 'http://url.to.image';
	$image->local_object_id = 0;
	$image->id = 1;

	$extra_inclusion .= json_encode($image);

	$post1 = new Scoopit\Entities\Post();
	$post1->image = $image;
	$post1->state='published';
	$post1->local_object_id=0;
	$post1->local_type='article';
	$post1->publicationDate='d-m-Y H:i:s';
	$post1->id=1;
	$post1->content='this is post '.$post1->id.' content';
	$post1->summary = 'This is summary for post '.$post1->id;
	$post1->title = 'Title for post '.$post1->id;
	$post1->url = 'http://url.original.post'.$post1->id;
	$post1->tags = array('tag1','tag2','tag3',);

	$post2 = new Scoopit\Entities\Post();
	$post2->image = $image;
	$post2->state='published';
	$post2->local_object_id=0;
	$post2->local_type='article';
	$post2->publicationDate='d-m-Y H:i:s';
	$post2->id=2;
	$post2->content='this is post '.$post2->id.' content';
	$post2->summary = 'This is summary for post '.$post2->id;
	$post2->title = 'Title for post '.$post2->id;
	$post2->url = 'http://url.original.post'.$post2->id;

	$post3 = new Scoopit\Entities\Post();
	$post3->image = $image;
	$post3->state='published';
	$post3->local_object_id=0;
	$post3->local_type='article';
	$post3->publicationDate='d-m-Y H:i:s';
	$post3->id=3;
	$post3->content='this is post '.$post3->id.' content';
	$post3->summary = 'This is summary for post '.$post3->id;
	$post3->title = 'Title for post '.$post3->id;
	$post3->url = 'http://url.original.post'.$post3->id;

	$post4 = new Scoopit\Entities\Post();
	$post4->image = $image;
	$post4->state='published';
	$post4->local_object_id=0;
	$post4->local_type='article';
	$post4->publicationDate='d-m-Y H:i:s';
	$post4->id = 4;
	$post4->content='this is post '.$post4->id.' content';
	$post4->summary = 'This is summary for post '.$post4->id;
	$post4->title = 'Title for post '.$post4->id;
	$post4->url = 'http://url.original.post'.$post4->id;

	$extra_inclusion .= "<br/>";
	$extra_inclusion .= "Single Post Request scoop_it_data: ";
	$extra_inclusion .= "<br/>";
	$extra_inclusion .= json_encode($post1);

	$extra_inclusion .= "<br/>";
	$extra_inclusion .= "Mulitple Post Request scoop_it_data: ";
	$extra_inclusion .= "<br/>";
	$extra_inclusion .= json_encode(array($post1,$post2,$post3,$post4,));

	$test = json_encode(array($post1,$post2,$post3,$post4,));
	$testObjList = json_decode($test);
	$extra_inclusion .= json_encode(array(1,2,3,4));

	*/

	$output = t('<h2>Add a new mapping:</h2>');
	$mapping_form = drupal_get_form('scoopit_mapping_settings_form');
	$output .= drupal_render($mapping_form);
	$output .= "<br/>";
	$output .= "<pre>";
	$output .= $extra_inclusion;
	$output .= "<pre/>";
	$output .= "<br/>";
	$output .= t('<h2>Or click on the Delete links to remove an existing mapping:</h2>');
	$output .= scoopit_mapping_settings_list();
	return $output;
}

function scoopit_mapping_settings_list() {
// Output of table with the paging

	// display current mapping
	$sql = "SELECT * FROM scoopit_api_content_map ORDER BY created_date DESC";
	$result = db_query($sql);
	// create table
	$header = array('Local Content Type', 'Local Content Field', 'Remote Content Type', 'Remote Content Field','Created By', 'Created Date', 'Action');
	$rows = array();
	// Looping for filling the table rows
	while($data = $result->fetchObject()){
		// Fill the table rows
		//$createdDate = new DateTime($data->created_date);

		$rows[] = array(
			$data->local_type,
			$data->local_field_name,
			$data->remote_type,
			$data->remote_field_name,
			$data->created_by,
			date('d-m-Y H:i:s', $data->created_date),
			l('Delete ', SCOOPIT_API_SERVER_SCHEME.str_replace(array('///','//'),'/',SCOOPIT_API_LOCAL_SERVER."/admin").'/config/scoopit/scoopit-mapping-manager', array('query' => array('del_id' => $data->id))),
		);
	}


	return theme_table(
		array(
			"header" => $header,
			"rows" => $rows,
			"attributes" => array(),
			"sticky" => true, // Table header will be sticky
			"caption" => "Mapping List",
			"colgroups" => array(),
			"empty" => t("Mapping Table has no data!") // The message to be displayed if table is empty
		)
	);
}

function scoopit_mapping_settings_form() {

	# the values for the dropdown box
	$form['empty_options'] = array(
		'#type' => 'value',
		'#value' => array(''=>t('--- SELECT ---')),
	);

	//loading all drupal content type
	$local_types = node_type_get_types();
	$local_options=array(''=>t('--- SELECT ---'));
	foreach ($local_types as $type){
		$local_options[$type->type] = $type->name;
	}

	$field_format_val = '';

	# the values for the dropdown box
	$form['local_options'] = array(
		'#type' => 'value',
		'#value' => $local_options,
	);

	$sql = "SELECT * FROM scoopit_api_content_map ";
	$result = db_query($sql);
	$data = $result->fetchObject();
	if($data==NULL)
	{
		$form['local_type'] = array(
			'#title' => t('Local Content Type'),
			'#type' => 'select',
			'#description' => "Select Local Content Type.",
			'#options' => $form['local_options']['#value'],
			'#attributes' => array('onchange' => 'getLocalContentTypeFields(this)','id'=>'local_type'),
		);
	}
	else
	{
		$form['local_type'] = array(
			'#title' => t('Local Content Type'),
			'#type' => 'textfield',
			'#description' => "Local Content Type. To change the main content field text format remove all the current mappings.",
			//'#options' => $form['local_options']['#value'],
			'#attributes' => array('readonly' => 'readonly','id'=>'local_type'),
			'#value' => ''.$data->local_type,
		);

		$field_format_val = variable_get($data->local_type.'_field_format','');
	}

	$filter_formats_arr = filter_formats();

	$field_format_options = array();//array('full_html'=>'full html', 'filtered_html'=>'filtered html', 'plain_text'=>'plain text', 'php'=>'php');


	foreach ($filter_formats_arr as $item)
	{
		$field_format_options[$item->format]=$item->name;
	}

	if($field_format_val==='' || $form['local_type']['#type']==='select')
	{
		$form['local_type_field_format'] = array(
			'#title' => t('Field Format'),
			'#type' => 'select',
			'#description' => "Select Field Format.",
			'#options' => $field_format_options,
			'#attributes' => array('id'=>'local_type_field_format'),
			'#value' => $field_format_val,
		);
	}
	else
	{
		$form['local_type_field_format'] = array(
			//'#title' => t('Field Format'),
			'#type' => 'hidden',
			//'#description' => "Select Field Format.",
			//'#options' => $field_format_options,
			'#attributes' => array('id'=>'local_type_field_format'),
			'#value' => $field_format_val,
		);
	}

	$form['local_field'] = array(
		'#title' => t('Local Content Field'),
		'#type' => 'select',
		'#description' => "Select Local Content Field.",
		'#options' => $form['empty_options']['#value'],
		'#attributes' => array('id'=>'local_field'),
		//'#DANGEROUS_SKIP_CHECK'=>true,
		'#validated'=> TRUE,
	);

	// Scanning the directory for Scoopit Entities
	$remote_entities = DnaMiscUtility::getFilesInFolder(dirname(__FILE__).'/dna.libraries/Scoopit/Entities',false,'.php');
	$remote_options=array(''=>t('--- SELECT ---'));
	foreach ($remote_entities as $entity)
	{
		$entity_name = basename($entity,'.php');
		$remote_options[$entity_name] = $entity_name;
	}

	# the values for the Remote Content dropdown box
	$form['remote_options'] = array(
		'#type' => 'value',
		'#value' => $remote_options,
	);

	$form['remote_type'] = array(
		//'#title' => t('Remote Content Type'),
		'#type' => 'hidden',
		//'#description' => "Select Remote Content Type.",
		//'#options' => $form['remote_options']['#value'],
		//'#attributes' => array('onchange' => 'getRemoteContentTypeFields(this)'),
		'#attributes' => array('id'=>'remote_type'),
		'#value' => 'Post',
	);

	$form['remote_field'] = array(
		'#title' => t('Remote Post Content Field'),
		'#type' => 'select',
		'#description' => "Select Remote Content Field.",
		'#options' => $form['empty_options']['#value'],
		//'#DANGEROUS_SKIP_CHECK'=>true,
		'#attributes' => array('id'=>'remote_field'),
		'#validated'=> TRUE,
	);

	global $base_path;
	$server_doc_root = $base_path;

	$form['dna_server_root'] = array(
		'#type' => 'hidden',
		'#attributes' => array('id'=>'dna_server_root'),
		'#value' => $server_doc_root,
		'#validated'=> TRUE,
	);

  return system_settings_form($form);
}

function scoopit_author_settings() {


	$extra_inclusion = "";
	if (function_exists('curl_version'))
	{
	}
	else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen'))
	{
	}
	else
	{
		$extra_inclusion = 'You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!';
	}

	$output = t('<h2>Choose a role for authors:</h2>');
	$author_form = drupal_get_form('scoopit_author_role_settings_form');
	$output .= drupal_render($author_form);
	$output .= "<br/>";
	$output .= "<pre>";
	$output .= $extra_inclusion;
	$output .= "<pre/>";
	$output .= "<br/>";
	$output .= t('<h2>Choose the primary author from list of authors below:</h2>');
	$output .= t('<div id="authorFeedBack"></div>');
	$output .= scoopit_author_settings_list();
	return $output;
}

function scoopit_author_settings_list() {
// Output of table with the paging

	$author_role = variable_get('scoopit_author_role', '');

	$author_id = variable_get('scoopit_author_id', '');


	// display current scoppit users authors
	$result = entity_load('user');
	// create table
	$header = array('Id', 'Username', /*'Role',*/ 'Action');
	$rows = array();
	// Looping for filling the table rows
	foreach($result as $data){
		// Fill the table rows

		if(user_has_roles($author_role,$data)){
			$rows[] = array(
				$data->uid,
				$data->name,
				//$data->roleid,
				'<input type="radio" name="scoopItUserSel" value="'.$data->uid.'" '.(($author_id==$data->uid)?' checked="checked" ':' ').' onclick="saveScoopitAuthor(this);" />',
			);
		}

	}


	return theme_table(
		array(
			"header" => $header,
			"rows" => $rows,
			"attributes" => array(),
			"sticky" => true, // Table header will be sticky
			"caption" => "Author List",
			"colgroups" => array(),
			"empty" => t("Author Table has no data!") // The message to be displayed if table is empty
		)
	);
}

function scoopit_author_role_settings_form() {

	# the values for the dropdown box

	$userrolesArr = user_roles();
	$sel_role = variable_get('scoopit_author_role', '');

	$role_options=array(''=>t('--- SELECT ---'));
	foreach ($userrolesArr as $role){
		$role_options[$role] = $role;
	}

	# the values for the dropdown box
	$form['role_options'] = array(
		'#role' => 'value',
		'#value' => $role_options,
	);

	$form['scoopit_role'] = array(
		'#title' => t('Scoopit Author Role'),
		'#type' => 'select',
		'#description' => "Select Author Role.",
		'#options' => $form['role_options']['#value'],
		'#attributes' => array('id'=>'scoopit_role'),
	);

	if($sel_role!=NULL && $sel_role!='')
	{
		$form['scoopit_role']['#default_value'] = array( $sel_role );
	}



	global $base_path;
	$server_doc_root = $base_path;

	$form['dna_server_root'] = array(
		'#type' => 'hidden',
		'#attributes' => array('id'=>'dna_server_root'),
		'#value' => $server_doc_root,
		'#validated'=> TRUE,
	);

  return system_settings_form($form);
}

//can be used in access callback too
function user_has_roles($roles,$user_var) {
	//checks if user has role/roles
	return !!count(array_intersect(is_array($roles)? $roles : array($roles), array_values($user_var->roles)));
};