/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//function to test if the passed object is an array
function isArray(testobj){
	if ( testobj instanceof Array )
		return true;
	else
		return false;
}

//function to do redirection
function redirection(url){
	window.location = url;
}

//get object from the object name
function get_object(obj_id){
	return document.getElementById(obj_id);
}

// function to collate all the input values to from specified list
function collateList(arg,list){
	var new_list = document.getElementById(list);
	list = new_list;

	var size_of_list = list.options.length;

	var int_arg = parseInt(arg);
	var value;
	var text;
	var string_list_options = "";
	//loop through the list
	if (size_of_list>0){
		for (var i = 0; i <size_of_list; i++){
			value = list.options[i].value;
			text = list.options[i].text;
			//lets skip first option
			if (value.trim()=='') continue;

			switch(int_arg){
				case 1:
					//plain all list options
					string_list_options+=value+"|!!|"+text+"!||!";
					break;
				case 2:
					//selected options
					//check if options are selected
					if (list.options[i].selected){
						//get the value and text
						string_list_options+=value+"|!!|"+text+"!||!";
					}
					break;
				case 3:
					//plain all list options
					string_list_options+=value+"!||!";
					break;
				case 4:
					//selected options
					//check if options are selected
					if (list.options[i].selected){
						//get the value and text
						string_list_options+=value+"!||!";
					}
				case 5:
					//plain all list options
					string_list_options+=text+"!||!";
					break;
				case 6:
					//selected options
					//check if options are selected
					if (list.options[i].selected){
						//get the value and text
						string_list_options+=text+"!||!";
					}
					break;
			}
		}
		return string_list_options;
	}
}

//function to read json data from server
function connvertTojsondata(data){
	var newdata = JSON.parse(data,function (key, value) {
		var type;
		if (value && typeof value === 'object') {
			type = value.type;
			if (typeof type === 'string' && typeof window[type] === 'function') {
				return new (window[type])(value);
			}
		}
		return value;
	} );
	return newdata;
}

function reloadPage()
{
	location.reload()
}

//function to process data on the page
function callprocess(code, form, feedbackdiv, param, url_arg, data, reqType, func,addProcessingImage){
	//alert ('hellok');
	///var url = '/ajax.php';

	//removeElement('script_trigger');

	//var param = '?'+param;
	//var target = feedbackdiv;
	var callmsg='<img id="proceImage" height="30" width="30" src="/images/ajax_loader.gif" title="Processing" alt="Processing" />';
	var url='';
	if(url_arg.indexOf('?')>=0){
		url=url_arg+'&'+param;
	}
	else {
		url=url_arg+'?'+param;
	}
	//alert(url);
	var new_feedback = document.getElementById(feedbackdiv);

	if(addProcessingImage)
		new_feedback.innerHTML+=callmsg;
	else
		new_feedback.innerHTML=callmsg;

	feedbackdiv = new_feedback;
	try {
		req = new XMLHttpRequest(); /* e.g. Firefox */
	} catch(e) {
		try {
			req = new ActiveXObject("Msxml2.XMLHTTP");
			/* some versions IE */
		} catch (e) {
			try {
				req = new ActiveXObject("Microsoft.XMLHTTP");
				/* some versions IE */
			} catch (E) {
				req = false;
			}
		}
	}
	//alert(url);
	//var data;
	try{
		req.onreadystatechange = function() { responseAHAH(form,feedbackdiv,code,data,func);};
		var parameter = param;
		if (reqType==null || reqType==false){
			req.open("GET",url,true);
			//req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		}
		else if(reqType==true){
			req.open("POST",url_arg,true);
			//req.setRequestHeader("Content-type", "multipart/form-data");
		}
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		//alert(url);
		req.send(parameter);
		//alert (data);
	}catch(ex){alert (ex);}
}

//actual point of sending and waiting for request to be processed
function responseAHAH(form, pageElement, code, data, func) {
	//$.noConflict();
	var output = '';
	//alert('hello');
	if(req.readyState == 4) {
		if(req.status == 200) {
			output = req.responseText;
			data = output;
			//alert(output);
			ackdata(code,pageElement,form,data,func);
		}
	}
}

//function to produce the feed back to caller
function ackdata(code, feedbackdiv, form, data, func){
	var arg = parseInt(code);

	feedbackdiv.innerHTML = data;
	try{
		//var script_trigger = document.getElementById('script_trigger');
		//script_trigger.onchange();
	}catch(e){}
	try{
		//var script_trigger2 = document.getElementById('script_trigger2');
		//script_trigger2.onchange();
	}catch(e){}

	try{eval(func)}catch(ex){}
}


function isNumeric(num){
	return !isNaN(num)
}

function get_elements_v2(parent){
	var elements = [];
	var searchEles = parent.children;
	for(var i = 0; i < searchEles.length; i++) {

		elements.push(searchEles[i]);

	}

	return elements;
}

function removeElement(id)
{
	try{
		return (elem=document.getElementById(id)).parentNode.removeChild(elem);//alert(id);
	}catch(e){
		//alert(e);	
	}
}
