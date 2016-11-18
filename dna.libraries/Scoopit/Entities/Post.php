<?php
/**
 * Created by PhpStorm.
 * User: dauduadetokunbo
 * Date: 25/07/2016
 * Time: 19:28
 */

/*
 *

id
    long - the id of the post
content
    string - post content in plain text
htmlContent
    string - post content in HTML
htmlFragment
    string - additional embedded HTML content if applicable (eg: embedded videos)
insight
    string - the insight of the curator
htmlInsight
    string - the html version of the insight
title
    string - the post title
thanksCount
    int - the number of times this post was thanked
reactionsCount
    int - the number reactions on this post
source
    source - the source of the post
twitterAuthor
    string - if source is a twitter search, the twitter user who wrote the original tweet
url
    string - original url of the post
scoopUrl
    string - url of the post on the scoop platform
scoopShortUrl
    string - shortened url of the post on the scoop platform
smallImageUrl
    string - url of the image chosen by the curator, referred below as "post image" (max width: 100px)
mediumImageUrl
    string - url of the image chosen by the curator, referred below as "post image" (max width: 200px)
imageUrl
    string - url of the image chosen by the curator, referred below as "post image" (max width: 400px)
largeImageUrl
    string - url of the image chosen by the curator, referred below as "post image" (max width: 1024px)
imageWidth
    int - width in pixel of the original post image
imageHeight
    int - height in pixel of the original post image
imageSize
    int - size of the post image in the topic view
imagePosition
    string - position of the post image in the topic view: "left" | "center" | "right"
imageUrls
    string[] - array of urls of image selector in curation mode
tags
    topic_tag[] - array of tags
commentsCount
    int - the number of comments for this post
isUserSuggestion
    boolean - true if the post is a user suggestion
suggestedBy
    user - the user that suggested this post (not present if the post is not a user suggestion)
pageViews
    long - number of time this post has been viewed
pageClicks
    long - number of time this post has been clicked
edited
    boolean - true if the description of this post has been manually edited by the curator
author
    user - the author of the post
publicationDate
    timestamp - the publication date of the original article
curationDate
    timestamp - the curation date of the post (aka the publication date on Scoop.it)
comments
    post_comment[] - the list of comments
thanked *
    boolean - true if the user has already thanked this post
topicId
    long - the id of the topic this post is belonging to
topic **
    topic - the topic this post is belonging to
scheduledDate **
    timestamp - the scheduled date of the article


 *
 */

/*
 *

text
    A simple text comment. The text field will hold the text typed by the user
share
    The post has been shared. The sharerId will hold the id of the sharer used to share the post (typically: twitter, facebook, linkedin...)
thank
    The post has been thanked
rescoop
    The post has been rescooped to another topic. The rescoopedPostId holds the id of the rescooped post

type
    enum - the type a the comment one of "text", "share", "thank", "rescoop" (see above)
text
    string - the comment text if applicable
sharerId
    string - the sharer id if applicable
rescoopedPostId
    long - id of the rescooped post if applicable
date
    timestamp - the comment date
author
    user - the comment author


 */
namespace Scoopit\Entities;

class Post
{
	public $id;
	// long - the id of the post (Integer & Unique / mandatory)
	public $publicationDate;
	//timestamp - the publication date of the original article (Date / mandatory)
	public $url;
	// string - original url of the post (String / mandatory)
	public $title;
	// string - the post title
	public $summary;
	// string - post summary in plain text
	public $state;
	// Could be published or scheduled
	public $image;
	// The image of the Scoop.it Post - ideally uploaded on the Drupal blog when content is created on SCD side
	public $tags;
	// topic_tag[] - array of tags
	public $scoopit_type = "Post";
	// string - the scoop it type to identify the remote object
	public $local_type;
	// string - the local drupal type to identify the local object to target
	public $local_object_id = 0;
	//long - for identifying local object
	public $content;
	// string - post content in plain text
	public $author;
	// string - post content in plain text
}