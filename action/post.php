<?php 
// action «post»



class UniversalValidator{

    private $validatorValues = [
        "type" => "",
        "name" => "",
        "value" => "",
        "checkPatternValidateFiltres" => true, 
        "patternFilterSettings" => [
            "patternValidateFilterName" => "",
            "patternFilterOption" => "",
            "patternFilterFlag" => "",
        ],
        
        "requiredValue" => false,

        "isCustom" => false,
        "customSettings" => [
            "customPattern" => "",
        ],

        "isMultiple" => false,   // true -> value is array
        "isCustomMultiple" => false,
        "multiplePatternSettings" => [
            "customMultiplePattern" => "", // Separating character, is false if empty
            "minCountValueChecked" => 0, //
            "maxCountValueChecked" => 0, //
        ],

        "resultData" => [],
        "errors" => [],
    ];

    private $defaultValidateFiltres = [
        "custom" => [], /* for using filters without type restriction */
		"url" => [
            "filter name" => FILTER_VALIDATE_URL,
            "flags" => [
                "FILTER_FLAG_SCHEME_REQUIRED" => FILTER_FLAG_SCHEME_REQUIRED,
                "FILTER_FLAG_HOST_REQUIRED" => FILTER_FLAG_HOST_REQUIRED,
                "FILTER_FLAG_PATH_REQUIRED" => FILTER_FLAG_PATH_REQUIRED,
                "FILTER_FLAG_QUERY_REQUIRED" => FILTER_FLAG_QUERY_REQUIRED,
            ]
        ],

		"email" => [
            "filter name" => FILTER_VALIDATE_EMAIL,
            "flags" => [
                "FILTER_FLAG_EMAIL_UNICODE" => FILTER_FLAG_EMAIL_UNICODE,
            ]
        ],

        "number" => [
            "filter name" => FILTER_VALIDATE_INT,
            "flags" => [
                "FILTER_FLAG_EMAIL_UNICODE" => FILTER_FLAG_ALLOW_OCTAL,
                "FILTER_FLAG_EMAIL_UNICODE" => FILTER_FLAG_ALLOW_HEX,
            ]
        ],

        "boolean" => [

        ],

        "domain" => [

        ],

        "float" => [

        ],

        "int" => [

        ],

        "ip" => [

        ],

        "mac_address" => [

        ],

        "regexp" => [

        ],

    ];
    
    function startValidate($processedData = []){
        
        $this -> $validatorValues["type"] = $processedData["type"];
        $this -> $validatorValues["name"] = $processedData["name"];
        $this -> $validatorValues["value"] =  /*$this -> treatmentDataFromVulnerabilities(*/$processedData["value"]/*)*/;
        $this -> $validatorValues["requiredValue"] = $processedData["requiredValue"];

        $this -> $validatorValues["checkPatternValidateFilters"] = $processedData["checkPatternValidateFilters"];
        $this -> $validatorValues["patternFilterSettings"]["patternValidateFilterName"] = $processedData["patternFilterSettings"]["patternValidateFilterName"];
        //$this -> $validatorValues["patternFilterSettings"]["patternFilterOption"] = $processedData["patternFilterSettings"]["patternFilterOption"];
        $this -> $validatorValues["patternFilterSettings"]["patternFilterFlag"] = $processedData["patternFilterSettings"]["patternFilterFlag"];

        $this -> $validatorValues["isCustom"] = $processedData["isCustom"];
        $this -> $validatorValues["customSettings"]["customPatternRegexp"] = $processedData["customSettings"]["customPatternRegexp"];

        $this -> $validatorValues["isMultiple"] = $processedData["isMultiple"];
        $this -> $validatorValues["isCustomMultiple"] = $processedData["isCustomMultiple"];
        $this -> $validatorValues["multiplePatternSettings"]["customMultiplePattern"] = $processedData["multiplePatternSettings"]["customMultiplePattern"];
        $this -> $validatorValues["multiplePatternSettings"]["minCountValueChecked"] = $processedData["multiplePatternSettings"]["minCountValueChecked"];
        $this -> $validatorValues["multiplePatternSettings"]["maxCountValueChecked"] = $processedData["multiplePatternSettings"]["maxCountValueChecked"];


        // checking required
        /*if ($this -> $validatorValues["requiredValue"]){
            $this -> required();
        }*/


        if ($this -> required()){

            // checking Multiple
            if ($this -> $validatorValues["isMultiple"]){
                $this -> multiple();
            }


            // checking customMultiple
            if ($this -> $validatorValues["isCustomMultiple"]){
                $this -> customMultiple();
            }


            // treatment Data From Vulnerabilities
            $this -> $validatorValues["value"] = $this -> treatmentDataFromVulnerabilities($this -> $validatorValues["value"]);


            // checking multiple Pattern Settings
            if ($this -> $validatorValues["isMultiple"] || $this -> $validatorValues["isCustomMultiple"]){
                $this-> customMultiplePatternSettings();
            }

            // checking custom validate settings
            if ($this -> $validatorValues["isCustom"]){            
                $this -> customValidate();
            }


            // checking pattern validate filters
            if ($this -> $validatorValues["checkPatternValidateFilters"]){
                $this -> checkPatternValidateFilters();
            }

        }
        
        // checking succsess validation
        if ($this -> isValidationSuccsess()){
            return true;
        }
        else{
            return false;
        }
    }


    public function getValidatorValue($property)
    {
        return $this -> $validatorValues[$property];
    }

    public function areThereErrors()
    {
        if (empty($this -> $validatorValues["errors"])){
            return false;
        }
        else{
            return true;
        }
    }

    private function treatmentDataFromVulnerabilities($data)
    {
        if (empty($data)){
            $this->setError("No data to process [treatment Data]");
            return false;
        }
        else
        {
            if($this -> $validatorValues["isMultiple"] || $this -> $validatorValues["isCustomMultiple"]){
                foreach ($data as $d){
                    $d = trim($d);
                    $d = stripslashes($d);
                    $d = htmlspecialchars($d);
                }
                return $data;
            }
            else{
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }
        }

    }

    private function multiple()
    {
        if (!is_array($this -> $validatorValues["value"])){
            $this->setError("The resulting value is not multiple");
            //return false;
        }
    }

    private function customMultiple()
    {
        $separatingCharacter = $this -> $validatorValues["multiplePatternSettings"]["customMultiplePattern"];
        if(!empty($separatingCharacter)){
            $newValue = explode($separatingCharacter, $this -> $validatorValues["value"]);
            $this -> $validatorValues["value"] = $newValue;
        }
    }

    private function customMultiplePatternSettings()
    {
        $value = $this -> $validatorValues["value"];
        $minCount = $this -> $validatorValues["multiplePatternSettings"]["minCountValueChecked"];
        $maxCount = $this -> $validatorValues["multiplePatternSettings"]["maxCountValueChecked"];


        if (empty($value)){
            $this->setError("No data to process [custom multiple pattern settings]");
            return false;
        }
        if(!empty($minCount)){
            if (count($value) < $minCount){
                $this->setError("Minimum data amount not reached");
                //return false;
            }
        }
        if(!empty($maxCount)){
            if (count($value) > $maxCount){
                $this->setError("Exceeded the amount of maximum data");
                //return false;
            }
        }
    }

    private function required()
    {
        if(empty($this -> $validatorValues["value"]))
        {
            $this->setError("Required field must be filled");
            return false;
        }
        else{
            return true;
        }

    }

    private function customValidate()
    {
        $value = $this -> $validatorValues["value"];
        $regexpValue = $this -> $validatorValues["customSettings"]["customPatternRegexp"];
        
        if (empty($regexpValue))
        {
            $this->setError("Custom pattern Regexp unfilled");
            return false;
        }
        else{
            $res = array("options"=>array("regexp"=>$regexpValue));

            if($this -> $validatorValues["isMultiple"] || $this -> $validatorValues["isCustomMultiple"]){

                foreach($value as $v){
                    $resultFilterValidate = filter_var($v, FILTER_VALIDATE_REGEXP, $res);
                    if ($resultFilterValidate){
                        //return true;
                    }
                    else{
                        $this->setError("Custom Multiple regex is incorrect");
                        return false;
                    }
                }
                return true;
            }
            else {

                $resultFilterValidate = filter_var($value, FILTER_VALIDATE_REGEXP, $res);
                if ($resultFilterValidate){
                    return true;
                }
                else{
                    $this->setError("Custom regex is incorrect");
                    return false;
                }
            }
        }
    }

    private function checkPatternValidateFilters()
    {

        if (array_key_exists($this -> $validatorValues["type"], $this -> defaultValidateFiltres)){
            if (!isset($this -> $validatorValues["patternFilterSettings"]["patternValidateFilterName"])
            //|| !isset($this -> $validatorValues["patternFilterSettings"]["patternFilterOption"])
            || !isset($this -> $validatorValues["patternFilterSettings"]["patternFilterFlag"])){
            
                $this->setError("The pattern filter validation settings are incorrect");
                return false;
            }
            else{

                $value = $this -> $validatorValues["value"];
                $filterName = $this -> $validatorValues["patternFilterSettings"]["patternValidateFilterName"];
                $filterFlag =  $this -> $validatorValues["patternFilterSettings"]["patternFilterFlag"];

                if (empty( $value) || empty($filterName)){
                    $this->setError("No data or filter name to process");
                    return false;
                }
                
                if($this -> $validatorValues["isMultiple"] || $this -> $validatorValues["isCustomMultiple"]){
                    foreach($value as $v){
                        $resultFilterValidate = filter_var($v, $filterName, $filterFlag);
                        if ($resultFilterValidate){
                            //return true;
                        }
                        else{
                            $this->setError("Invalid Multiple value [after Multiple filter validate]");
                            return false;
                        }
                    }
                }
                else{
                    $resultFilterValidate = filter_var($value, $filterName, $filterFlag);
                    if ($resultFilterValidate){
                        return true;
                    }
                    else{
                        $this->setError("Invalid value [after filter validate]");
                        return false;
                    }
                }
            }
        }
        else{

            $this->setError("The specified type is not supported in pattern filter validation");
            return false;
        }

    }

    private function setError($error)
    {
        $name = $this -> $validatorValues["name"];
        $this -> $validatorValues["errors"][$name][] = "$error";
    }

    private function setSuccsesResult($result)
    {
        $name = $this -> $validatorValues["name"];
        $this -> $validatorValues["resultData"][$name][] = "$result";
    }

    private function isValidationSuccsess()
    {
        $name = $this -> $validatorValues["name"];
        if (empty($this -> $validatorValues["errors"][$name])){
            $this -> setSuccsesResult("Validation succsess");
            return true;
        }
        else{
            return false;
        }
    }

}


//class ValidatorEmail extends UniversalValidator{}

$validator = new UniversalValidator();
$validator -> startValidate(
    $processedData = [
        "type" => "email", 
        "name" => "email", 
        "value" => $_POST['email'],
        "checkPatternValidateFilters" => true, 
        "patternFilterSettings" => [
            "patternValidateFilterName" => FILTER_VALIDATE_EMAIL,
            "patternFilterFlag" => FILTER_FLAG_EMAIL_UNICODE,
        ],
        "requiredValue" => true,

        "isCustom" => false,
        "customSettings" => [ 
            "customPatternRegexp" => "",
        ],
    ],
);

$validator -> startValidate(
    $processedData = [
        "type" => "text", 
        "name" => "text",
        "value" => $_POST['text'],

        "requiredValue" => true,

        "isCustom" => true,
        "customSettings" => [
            "customPatternRegexp" => ("/^.{3,20}$/"),
        ],

        "isMultiple" => false,
        "isCustomMultiple" => true,
        "multiplePatternSettings" => [
            "customMultiplePattern" => ",", // Separating character, is false if empty
            "minCountValueChecked" => 2, //
            "maxCountValueChecked" => 3, //
        ],
    ],
);

$validator -> startValidate(
    $processedData = [
        "type" => "custom", /* кастомный тип данных */
        "name" => "text2", /* */
        "value" => $_POST['text2'], /* */
        "checkPatternValidateFilters" => true, /* */
        "patternFilterSettings" => [
            "patternValidateFilterName" => FILTER_VALIDATE_EMAIL, /* */
            "patternFilterFlag" => FILTER_FLAG_EMAIL_UNICODE, /* */
        ],
        "requiredValue" => false, /* */

        "isCustom" => false, /* */
        "customSettings" => [
            "customPatternRegexp" => "", /* */
        ],

        "isMultiple" => false, /* */
        "isCustomMultiple" => true, /* */
        "multiplePatternSettings" => [
            "customMultiplePattern" => ",", /* Separating character, is false if empty */
            "minCountValueChecked" => "", /* */
            "maxCountValueChecked" => "", /* */
        ],
    ],
);

$validator -> startValidate(
    $processedData = [
        "type" => "number", 
        "name" => "number",
        "value" => $_POST['number'],
        "checkPatternValidateFilters" => true, 
        "patternFilterSettings" => [
            "patternValidateFilterName" => FILTER_VALIDATE_INT,
            "patternFilterFlag" => FILTER_FLAG_ALLOW_OCTAL,
        ],
        "requiredValue" => true,

        "isCustom" => true,
        "customSettings" => [
            "customPatternRegexp" => ("/^([5-9])$/"),
        ],
    ],
);

$validator -> startValidate(
    $processedData = [
        "type" => "url", 
        "name" => "url",
        "value" => $_POST['url'],
        "checkPatternValidateFilters" => true, 
        "patternFilterSettings" => [
            "patternValidateFilterName" => FILTER_VALIDATE_URL,
            "patternFilterFlag" => "",
        ],

        "requiredValue" => false,

        "isCustom" => false,
        "customSettings" => [
            "customPatternRegexp" => ("/^([5-9])$/"),
        ],
    ],
);

$validator -> startValidate(
    $processedData = [
        "type" => "checkbox", 
        "name" => "checkbox",
        "value" => $_POST["checkboxies"],

        "requiredValue" => true,

        "isMultiple" => true,   // use just isMultiple or isCustomMultiple parameter
        "isCustomMultiple" => false,
        "multiplePatternSettings" => [
            "customMultiplePattern" => "", // Separating character, is false if empty
            "minCountValueChecked" => 2, //
            "maxCountValueChecked" => 3, //
        ],
    ],
);

/* example for getting a result for personal use */
/*if ($validator -> startValidate(
    $processedData = [
        "type" => "checkbox", 
        "name" => "checkbox",
        "value" => $_POST["checkboxies"],

        "requiredValue" => true,

        "isMultiple" => true,   // use just isMultiple or isCustomMultiple parameter
        "isCustomMultiple" => false,
        "multiplePatternSettings" => [
            "customMultiplePattern" => "", // Separating character, is false if empty
            "minCountValueChecked" => 2, //
            "maxCountValueChecked" => 3, //
        ],
    ],
)){
    echo "Yeads, it works bosss ;)";
}
else{
    echo "Fuck!";
}*/


require BASE_DIR . 'layout/start.php';

if ($validator -> areThereErrors()){
    require BASE_DIR . "layout/error.php";
}
else{
    require BASE_DIR . "layout/result.php";
}

require BASE_DIR . 'layout/end.php';


?>