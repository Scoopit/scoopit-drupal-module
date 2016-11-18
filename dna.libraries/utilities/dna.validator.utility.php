<?php

class DnaValidatorUtility extends DnaMainUtility {

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
    
    //function to do the validation
    public function isValid($value,$case,$input=NULL,$required=NULL,$min_length=NULL,$max_length=NULL,$custom_info=NULL)
    {
        $validator = false;
        $validator2 = false;
        $err_msg = "";

		if(!$required && (strlen($value)<1 || $value==NULL)){
        	$validator = true;
		}
		else{
			$validator = $this->validate_length($value,$min_length,$max_length);
		}
		
        if (!$validator){
            if ($min_length!=NULL && $min_length>0){
                $err_msg .= (($input!=NULL && trim($input)!='')?$input:'')." Minimum Length Required is ".$min_length."<BR/>";
            }
            if ($max_length!=NULL && $max_length>0){
                $err_msg .= (($input!=NULL && trim($input)!='')?$input:'')." Maximum Length Required is ".$max_length."<BR/>";
            }
            //$this->global_session->info_status = 1;
        }
		
        $case = strtolower($case);
		
        switch($case)
        {
            case 'name':
                    $validator2 = $this->validate_name($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":$custom_info);
                    }
            break;
            case 'alpha':
                    $validator2 = $this->validate_alpha($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'alphanum':
                    $validator2 = $this->validate_alpha_numeric($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'alphanumspecial':
                    $validator2 = $this->validate_alpha_numeric_with_special($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'drupalfields':
                    $validator2 = $this->validate_alpha_numeric_with_drupalfields($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'alphaspecial':
                    $validator2 = $this->validate_alpha_numeric_with_special($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'alphaspecialspace':
                    $validator2 = $this->validate_alpha_special_with_space($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'numeric':
                    $validator2 = $this->validate_number($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'integer':
                    $validator2 = $this->validate_integer($value,$required);
					if (!$validator2){
						$err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
					}
            break;
            case 'date':
                    $validator2 = $this->validate_date($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'email':
                    $validator2 = $this->validate_email($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'phone':
                    $validator2 = $this->validate_phone_number($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'alphanumspecialspace':
                    $validator2 = $this->validate_alpha_numeric_with_special_space($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
			case 'note':
                    $validator2 = $this->validate_note($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'alphaspace':
                    $validator2 = $this->validate_alpha_space($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'alphanumspace':
                    $validator2 = $this->validate_alpha_numeric_with_space($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'numspecial':
                    $validator2 = $this->validate_numeric_with_special($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
			case 'username':
                    $validator2 = $this->validate_username($value,$required);
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }                                                                                                 
            break;
            case 'image':
                    $validator2 = $this->validate_image();//Not implemented
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            case 'dropdown':
                    $validator2 = $this->validate_dropdown();//Not implemented
                    if (!$validator2){
                        $err_msg .= (($input!=NULL && trim($input)!='')?$input:'input').(($custom_info==NULL)?" requirement (".$this->getDescription($case).")":" ".$custom_info);
                    }
            break;
            
			case 'none':
                    $validator2 = true;
            break;
        }

        if(!$validator2){
            $err_msg .= "<BR/>";
            //$this->global_session->info_status = 1;
        }

        //lets add a notification 
        //$this->global_session->info .= $err_msg;

		$response = new Validation_Response(($validator == true && $validator2 == true),$err_msg);
	
        return $response;
		
    }
	
    //this function checks for length violation and returns a true or false on satisfaction of the rule;
    public function validate_length($arg,$required_minimum_length=NULL,$required_maximum_length=NULL)
    {
        $validator = true;
        $len_arg = strlen($arg);

        if ($required_minimum_length!=NULL && $required_maximum_length!=NULL){//Minimum and Maximum length required only
            if ($len_arg>=$required_minimum_length && $len_arg<=$required_maximum_length){//test valid
                $validator = true;
            }else{//test invalid
                $validator = false;
            }
        }else if ($required_minimum_length!=NULL && $required_maximum_length==NULL){//minimum length required only
            if ($len_arg>=$required_minimum_length){//test valid
                $validator = true;
            }else{//test invalid
                $validator = false;
            }
        }else if ($required_minimum_length==NULL && $required_maximum_length!=NULL){//maximum length required only
            if ($len_arg<=$required_maximum_length){//test valid
                $validator = true;
            }else{//test invalid
                $validator = false;
            }
        }
        else{//length is not required
            $validator = true;	
        }

        //the test value
        return $validator;
    }

    //this function checks for email violation and returns a true or false on satisfaction of the rule;
    public function validate_email($email_arg,$required=false)
    {
        $validator = true;

        if ($required){//email is required
            //$validator = new Zend_Validate_EmailAddress();
            if(filter_var($email_arg,FILTER_VALIDATE_EMAIL)) {//!filter_var($email, FILTER_VALIDATE_EMAIL)
                // it's valid 
                $validator = true;
            }else{
               // it's not valid 
               $validator = false;
            }
        }
        else{//email is not required
            if (trim($email_arg)!=''){//email is not empty
                $validator = filter_var($email_arg,FILTER_VALIDATE_EMAIL);//$this->validate_email($email_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for telephone number violation and returns a true or false on satisfaction of the rule;
    public function validate_phone_number($phone_number_arg,$required=false)
    {
        $validator = true;
        if ($required){//phone number is required
            //009647701570925
            if(preg_match("/^[0-9]{3}[0-9]{4}[0-9]{4}$/", $phone_number_arg) || preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $phone_number_arg)|| preg_match("/^[+]{1}[1-9]{1,3}[0-9]{2}[0-9]{4}[0-9]{4}$/", $phone_number_arg) || preg_match("/^[+]{1}[1-9]{1,3}-[0-9]{2}-[0-9]{4}-[0-9]{4}$/", $phone_number_arg) || preg_match("/^[0]{2}[1-9]{1,3}-[0-9]{2}-[0-9]{4}-[0-9]{4}$/", $phone_number_arg) || preg_match("/^[0]{2}[1-9]{1,3}[0-9]{2}[0-9]{4}[0-9]{4}$/", $phone_number_arg)) {
                // it's valid 
                $validator = true;
            }else{	
                // it's not valid 
               $validator = false;
            }
        }else{//phone number is not required
            if (trim($phone_number_arg)!=''){//phone number is not empty
                $validator = $this->validate_phone_number($phone_number_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for name= violation and returns a true or false on satisfaction of the rule;
    public function validate_name($alpha_arg, $required = false)
    {
        $validator = true;

        if ($required){//alpha is required
            if(preg_match("/^[a-zA-Z\' \-]+$/",$alpha_arg)) {
                // it's valid 
                $validator = true;
            }else{
               // it's not valid 
               $validator = false;
            }
        }else{//alpha is not required
            if (trim($alpha_arg)!=""){//alpha is not empty
                $validator = $this->validate_name($alpha_arg, true);
            }
        }

        //the test value
        return $validator;
    }
	
	//this function checks for company name= violation and returns a true or false on satisfaction of the rule;
    public function validate_username($alpha_arg, $required = false)
    {
        $validator = true;

        if ($required){//alpha is required
            if(preg_match("/^[a-zA-Z0-9\$\-_@]+$/",$alpha_arg)) {
                // it's valid 
                $validator = true;
            }else{
               // it's not valid 
               $validator = false;
            }
        }else{//alpha is not required
            if (trim($alpha_arg)!=""){//alpha is not empty
                $validator = $this->validate_username($alpha_arg, true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for number/numeric violation and returns a true or false on satisfaction of the rule;
    public function validate_number($number_arg,$required=false)
    {
        $validator = true;

        if ($required){//number is required
            if(is_numeric($number_arg) ) {
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//number is not required
            if (trim($number_arg)!=''){//number is not empty
                $validator = $this->validate_number($number_arg,true);
            }
        }

        //the test value
        return $validator;
    }
    
    //this function checks for integer violation and returns a true or false on satisfaction of the rule;
    public function validate_integer($number_arg,$required=false)
    {
        $validator = true;
		
        if ($required){//number is required
            if(is_numeric( $number_arg ) && (strpos( $number_arg, '.' ) === false)) {
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//number is not required
            if (trim($number_arg)!=''){//number is not empty
                $validator = $this->validate_integer($number_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for alphabets violation and returns a true or false on satisfaction of the rule;
    public function validate_alpha($alpha_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha is required
            if(preg_match('/^[a-zA-Z]+$/',$alpha_arg)) {
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//alpha is not required
            if (trim($alpha_arg)!=''){//alpha is not empty
                $validator = $this->validate_alpha($alpha_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for alpha with space violation and returns a true or false on satisfaction of the rule;
    public function validate_alpha_space($alpha_space_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha-numeric is required
                if(preg_match('/^[a-zA-Z ]+$/',$alpha_space_arg)) {
                    // it's valid 
                    $validator = true;
                }else {
                   // it's not valid 
                   $validator = false;
                }
        }else{//alpha-numeric is not required
            if (trim($alpha_space_arg)!=''){//alpha-numeric is not empty
                $validator = $this->validate_alpha_space($alpha_space_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for alpha-numeric violation and returns a true or false on satisfaction of the rule;
    public function validate_alpha_numeric($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha-numeric is required
            if(preg_match('/^[a-zA-Z0-9]+$/',$alpha_num_arg)) {
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//alpha-numeric is not required
            if (trim($alpha_num_arg)!=''){//alpha-numeric is not empty
                $validator = $this->validate_alpha_numeric($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for alpha-numeric and including space violation and returns a true or false on satisfaction of the rule;
    public function validate_alpha_numeric_with_space($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha-numeric is required
            if(preg_match('/^[a-zA-Z0-9 ]+$/',$alpha_num_arg)){
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//alpha-numeric is not required
            if (trim($alpha_num_arg)!=''){//alpha-numeric is not empty
                $validator = $this->validate_alpha_numeric_with_space($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for date violation and returns a true or false on satisfaction of the rule;
    public function validate_date($date_arg,$required=false)
    {
        $validator = true;

        if ($required){//date is required
            $datasplit = explode('-',$date_arg);
			//print_r($datasplit); die();
            if(checkdate ($datasplit[1] , $datasplit[2] , $datasplit[0])){
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//date is not required
            if (trim($date_arg)!=''){//date is not empty
                $validator = $this->validate_date($date_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for alpha and space violation and returns a true or false on satisfaction of the rule;
    public function validate_alpha_special_with_space($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha-special with space is required
            if(preg_match('/^[a-zA-Z0-9 \'!&\/\-()]+$/',$alpha_num_arg)){
                // it's valid 
                $validator = true;
            }
            else {
               // it's not valid 
               $validator = false;
            }
        }else{//alpha-special with space is not required
            if (trim($alpha_num_arg)!=''){//alpha-special with space is not empty
                $validator = $this->validate_alpha_special_with_space($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for alpha-numeric and including special characters such as '- violation and returns a true or false on satisfaction of the rule;
    public function validate_alpha_numeric_with_special($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha-numeric is required
            if(preg_match('/^[a-zA-Z0-9\'!&\/\-()]+$/',$alpha_num_arg)){
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//alpha-numeric is not required
            if (trim($alpha_num_arg)!=''){//alpha-numeric is not empty
                $validator = $this->validate_alpha_numeric_with_special($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }

    //this function checks for drupal field and including special characters such as _ violation and returns a true or false on satisfaction of the rule;
    public function validate_alpha_numeric_with_drupalfields($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//drupal field is required
            if(preg_match('/^[a-zA-Z0-9\_]+$/',$alpha_num_arg)){
                // it's valid
                $validator = true;
            }else {
               // it's not valid
               $validator = false;
            }
        }else{//drupal field is not required
            if (trim($alpha_num_arg)!=''){//drupal field is not empty
                $validator = $this->validate_alpha_numeric_with_drupalfields($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }
	
	public function validate_alpha_numeric_with_special_space($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha-numeric is required
			//
            if(preg_match('/^[a-zA-Z0-9\- .,!&\/()]+$/',$alpha_num_arg)){
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//alpha-numeric is not required
            if (trim($alpha_num_arg)!=''){//alpha-numeric is not empty
                $validator = $this->validate_alpha_numeric_with_special_space($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }
	
	public function validate_note($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//alpha-numeric is required
			//
            if(preg_match('/^[a-zA-Z0-9\ ,.\-:]+$/',$alpha_num_arg)){
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//alpha-numeric is not required
            if (trim($alpha_num_arg)!=''){//alpha-numeric is not empty
                $validator = $this->validate_note($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }
	
	//function valid extension
	public function validExtension($file_name,$arr_extentions,$file_type=NULL,$arr_file_types=NULL)
	{
		
		$tname = explode('.', $file_name);
        $ext = (strtolower($tname[sizeof($tname) - 1]));
		
		if ($arr_file_types!=NULL)
		{
			foreach($arr_file_types as $type){
				if ($file_type == $type)
				{
					$found = true;
					break;
				}
			}
			
			if (!$found){
				 return false;	
			}
		}
		
		foreach($arr_extentions as $extension)
		{
			if (strtolower($extension)==strtolower($ext)){
				$foundExt = true;
				break;
			}
			
		}
		
		if (!$foundExt){
			 return false;	
		}
		
		return true;
	}

    //this function checks for numeric and including special characters such as '- violation and returns a true or false on satisfaction of the rule;
    public function validate_numeric_with_special($alpha_num_arg,$required=false)
    {
        $validator = true;

        if ($required){//numeric-special is required
            if(preg_match('/^[a-zA-Z0-9\'-]+$/',$alpha_num_arg)){
                // it's valid 
                $validator = true;
            }else {
               // it's not valid 
               $validator = false;
            }
        }else{//numeric-special is not required
            if (trim($alpha_num_arg)!=''){//numeric-special is not empty
                $validator = $this->validate_numeric_with_special($alpha_num_arg,true);
            }
        }

        //the test value
        return $validator;
    }
    
    //this function validates an uploaded image
    public function validate_image(){
		
		return false;
	}
    //This functin validates a dropdown field
    public function validate_dropdown(){
		
		return false;
	}
    
	public function getValidationProperty(){
		return false;
	}
	
	//function to get the validation name
    public function getValidationName($valid_id) 
    {
		return false;
	}
	
	//function to retrieve a form field description                       
    public function getDescription($case)
    {
       $case = strtolower($case);
       switch($case)
       {
            case 'name':
                   return "Enter only alphabets. No spaces or numbers";
            break;
            case 'alpha':
                    return "Enter only alphabets.No spaces or numbers";
            break;
            case 'alphanum':
                    return "Enter only alphabets and numbers.No spaces";
            break;
            case 'alphanumspecial':
                    return "Enter only alphabets, numbers and symbols.No spaces";
            break;
            case 'drupalfields':
                    return "Enter only alphabets, numbers and or underscore (_).No spaces";
            break;
            case 'alphaspecial':
                    return "Enter only alphabets and symbols.No spaces";
            break;
            case 'alphaspecialspace':
                    return "Enter only alphabets and symbols and spaces";
            break;
            case 'numeric':
                   return "Enter only numbers e.g (5, 2.2)";
            break;
            case 'integer':
                   return "Enter only integers e.g (5,100)";
            break;
            case 'date':
                    return "Enter a valid date(e.g 2012-10-05)";
            break;
            case 'email':
                    return "e.g. you@youremail.com";
            break;
            case 'phone':
                   return "Can contain country code(e.g +447443237370 ,07412940481, 00441509852927, +441509852927, 01509852927) Please Make Sure there is NO Space";
            break;
            case 'alphanumspecialspace':
                    return "Enter alphabets, numbers and symbols";
            break;
            case 'note':
                    return "Enter alphabets, numbers and symbols";
            break;
            case 'alphaspace':
                   return "Enter only alphabets";
            break;
            case 'alphanumspace':
                   return "Enter alphabets, numbers and symbols";
            break;
            case 'numspecial':
                    return "Enter numbers and symbols";
            break;
            case 'username':
                   return "Enter alphabets, numbers and symbols(&, (-), (_), (~), @, $, (.) or *)";
            break;
            case 'image':
                   return"";
            break;
            case 'dropdown':
                   return "Choose from available options";
            break;
            case 'none':
                    return "";
            break;
        }
    }
	
	/*private function getFilterGrammar()
	{
		return (?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*:(?:(?:\r\n)?[ \t])*(?:(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*)(?:,\s*(?:(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*|(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)*\<(?:(?:\r\n)?[ \t])*(?:@(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*(?:,@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*)*:(?:(?:\r\n)?[ \t])*)?(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|"(?:[^\"\r\\]|\\.|(?:(?:\r\n)?[ \t]))*"(?:(?:\r\n)?[ \t])*))*@(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*)(?:\.(?:(?:\r\n)?[ \t])*(?:[^()<>@,;:\\".\[\] \000-\031]+(?:(?:(?:\r\n)?[ \t])+|\Z|(?=[\["()<>@,;:\\".\[\]]))|\[([^\[\]\r\\]|\\.)*\](?:(?:\r\n)?[ \t])*))*\>(?:(?:\r\n)?[ \t])*))*)?;\s*);	
		}*/
}

//for easy return of response
class Validation_Response {
	
	public $message;
	public $result;
	
	public function __construct($result,$message)
	{
		$this->message = $message;
		$this->result = $result;
	}
}
