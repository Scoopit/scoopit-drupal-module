<?php
/**
 * Created by PhpStorm.
 * User: dauduadetokunbo
 * Date: 25/07/2016
 * Time: 19:28
 */

namespace Scoopit\Entities\Sub;

class Image
{
 	public $id;
  	// long - the id of the post (Integer & Unique / mandatory) Unique way to identified an image
	public $url;
	// string - original url of the post (String / mandatory) Image url on Drupal side

	public $scoopit_type = "Image";
	// string - the scoop it type to identify the remote object
	//public $local_object_id = 0;
	//long - for identifying local object
	//public $local_type;
	// string - the local drupal type to identify the local object to target
}