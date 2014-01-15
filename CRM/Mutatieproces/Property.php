<?php

/**
 * Class Property for dealing with properties (De Goede Woning)
 * 
 * @author Erik Hommel (erik.hommel@civicoop.org, http://www.civicoop.org)
 * @date 6 Jan 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A.
 * Licensed to De Goede Woning under the Academic Free License version 3.0.
 */
class CRM_Mutatieproces_Property {
    private $_table = "";
    private $_type_table = "";
    public $id = 0;
    public $vge_id = 0;
    public $complex_id = 0;
    public $subcomplex = "";
    public $vge_street_name = "";
    public $vge_street_number = 0;
    public $vge_street_unit = "";
    public $vge_postal_code = "";
    public $vge_city = "";
    public $vge_country_id = 0;
    public $vge_address_id = 0;
    public $epa_label = "";
    public $epa_pre = "";
    public $city_region = "";
    public $block = "";
    public $vge_type_id = 0;
    public $strategy_label = "";
    public $strategy_b_pnts = 0;
    public $strategy_c_pnts = 0;
    public $number_rooms = 0;
    public $outside_code = "";
    public $build_year = 0;
    public $stairs = 0;
    public $square_mtrs = "";
    /**
     * constructor
     */
    function __construct() {
        $this->_table = 'civicrm_property';
        $this->_type_table = 'civicrm_property_type';
    }
    /**
     * function to add a property
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param array $params Array with parameters for field values (expecting field names as elements)
     */
    function create($params) {
        $query_fields = $this->_setPropertyFields($params);
        if (!empty($query_fields)) {
            $query = "INSERT INTO ".$this->_table." SET ".implode(", ", $query_fields);
            CRM_Core_Error::debug("query insert prop", $query);
            CRM_Core_DAO::executeQuery($query);
        }
        $latest_query = "SELECT MAX(id) AS max_id FROM ".$this->_table;
        $dao_latest = CRM_Core_DAO::executeQuery($latest_query);
        if ($dao_latest->fetch()) {
            if (isset($dao_latest->max_id)) {
                $this->id = $dao_latest->max_id;
            }
        }        
    }
    /**
     * function to update a property
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param array $params Array with parameters for field values (expecting field names as elements)
     * @return $property object with data of created or updated property
     */
    public function update($params) {
        $query_fields = $this->_setPropertyFields($params);
        if (!empty($query_fields)) {
            $query = "UPDATE ".$this->_table." SET ".implode(", ", $query_fields)." WHERE id = {$this->id}";
            CRM_Core_DAO::executeQuery($query);
        }
    }
    /**
     * static function to retrieve property with vge_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param integer $vge_id
     * @return array $result
     */
    public static function getByVgeId($vge_id) {
        $result = array();

        if (empty($vge_id) || !is_numeric($vge_id)) {
            $result['is_error'] = 1;
            $result['error_message'] = "Vge_id empty or not an integer";
            return $result;
        }
        
        $query = "SELECT * FROM civicrm_property WHERE vge_id = $vge_id";
        $dao_property = CRM_Core_DAO::executeQuery($query);
        if ($dao_property->fetch()) {
            $result = self::_propertyToArray($dao_property);
        } else {
            $result['count'] = 0;
        }
        return $result;
    }
    /**
     * function to set fields based on incoming params
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * 
     * @param array $params expecting fields
     * @return array $result with fields
     */
    private function _setPropertyFields($params) {
        $result = array();
        
        if (isset($params[0])) {
            $this->vge_id = $params[0];
            $result[] = "vge_id = {$this->vge_id}";
        }
        
        if (isset($params[1])) {
            $this->complex_id = CRM_Core_DAO::escapeString($params[1]);
            $result[] = "complex_id = '{$this->complex_id}'";
        }
              
        if (isset($params[5])) {
            $this->vge_street_name = CRM_Core_DAO::escapeString($params[5]);
            $result[] = "vge_street_name = '{$this->vge_street_name}'";
        }

        if (isset($params[6])) {
            $this->vge_street_number = $params[6];
            $result[] = "vge_street_number = {$this->vge_street_number}";
        }
        
        if (isset($params[8])) {
            $this->vge_street_unit = CRM_Core_DAO::escapeString($params[8]);
            $result[] = "vge_street_unit = '{$this->vge_street_unit}'";
        }
        
        if (isset($params[9])) {
            $this->vge_postal_code = self::formatPostalCode($params[9]);
            $result[] = "vge_postal_code = '{$this->vge_postal_code}'";
        }
        
        if (isset($params[10])) {
            $this->vge_city = CRM_Core_DAO::escapeString($params[10]);
            $result[] = "vge_city = '{$this->vge_city}'";
        }
        
        $this->vge_country_id = 1152;
        $result[] = "vge_country_id = {$this->vge_country_id}";
        
        if (isset($params[13])) {
            $this->epa_label = CRM_Core_DAO::escapeString($params[13]);
            $result[] = "epa_label = '{$this->epa_label}'";
        }
        
        if (isset($params[14])) {
            $this->epa_pre = CRM_Core_DAO::escapeString($params[14]);
            $result[] = "epa_pre = '{$this->epa_pre}'";
        }
        
        if (isset($params[2])) {
            $this->city_region = CRM_Core_DAO::escapeString($params[2]);
            $result[] = "city_region = '{$this->city_region}'";
        }
        
        if (isset($params[3])) {
            $this->block = CRM_Core_DAO::escapeString($params[3]);
            $result[] = "block = '{$this->block}'";
        }
        
        if (isset($params[11]) && !empty($params[11])) {
            $type_exists = $this->_getPropertyTypeId($params[11]);
            if ($type_exists == FALSE) {
                $this->_createPropertyType($params[11]);
            }
            $result[] = "vge_type_id = {$this->vge_type_id}";
        }
        
        if (isset($params[4])) {
            $this->square_mtrs = CRM_Core_DAO::escapeString($params[4]);
            $result[] = "square_mtrs = '{$this->square_mtrs}'";
        }
        
        if (isset($params[12])) {
            if (is_numeric($params[12])) {
                $this->build_year = $params[12];
            }
            $result[] = "build_year = {$this->build_year}";
        }
       
        return $result;
    }
    /**
     * static function to format the postal code 1234 AA
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param string $postal_code
     * @return string $formatted_postal_code
     */
    public static function formatPostalCode($postal_code) {
        $formatted_postal_code = $postal_code;
        /*
         * only format if length incoming string is 6
         * and pattern is 1234AA
         */
        if (!empty($postal_code) && strlen($postal_code == 6)) {
            if (is_numeric(substr($postal_code, 0, 4))) {
                if (is_string(substr($postal_code, 4, 2))) {
                    $formatted_postal_code = substr($postal_code, 0, 4)." ".substr($postal_code, 4, 2);
                }
            }
        }
        return $formatted_postal_code;
    }
    /**
     * static function to check if the address_id exists in CiviCRM
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 6 Jan 2014
     * @param type $address_id
     * @return boolean true or false
     */
    public static function validAddressId($address_id) {
        if (!is_integer($address_id)) {
            return FALSE;
        }
        try {
            $api_address = civicrm_api3('Address', 'Getcount', array('id' => $address_id));
            $count_address = $api_address;
        } catch (CiviCRM_API3_Exception $e) {
            $count_address = 0;
        }
        if ($count_address == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * static function to check if the country_id exists in CiviCRM
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 6 Jan 2014
     * @param type $country_id
     * @return boolean true or false
     */
    public static function validCountryId($country_id) {
        if (!is_integer($country_id)) {
            return FALSE;
        }
        try {
            $api_country = civicrm_api3('Country', 'Getcount', array('id' => $country_id));
            $count_country = $api_country;
        } catch (CiviCRM_API3_Exception $e) {
            $count_country = 0;
        }
        if ($count_country == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * static function to set the vge_type_id based on name
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 15 Jan 2014
     * @param string $vge_type
     * @return boolean TRUE/FALSE
     */
    private function _getPropertyTypeId($vge_type) {
        if (empty($vge_type)) {
            return FALSE;
        }
        $query = "SELECT id FROM ".$this->_type_table." WHERE label = '$vge_type'";
        $dao = CRM_Core_DAO::executeQuery($query);
        if ($dao->fetch()) {
            $this->vge_type_id = $dao->id;
        }
        return TRUE;
    }
    /**
     * private function to store dao property into array
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param object $property
     * @return array $result
     */
    private static function _propertyToArray($property) {
        $result = array();
        $property_fields = get_object_vars($property);
        foreach ($property_fields as $field_name => $field_value) {
            if (substr($field_name, 0, 1) != "_" && $field_name != "N") {
                $result[$field_name] = $field_value;
            }
        }
        return $result;
    }
    /**
     * Function to store property data in custom fields for
     * all cases huuropzegging en mutatie
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 9 Jan 2014
     * @param array $params
     * @return array $result (is_error, can be 1 or 0 and optional error_message)
     */
    public function setHuuropzeggingCustomFields() {
        $result = array();
        /*
         * vge_id is required
         */
        if (empty($this->vge_id)) {
            $result['is_error'] = 1;
            $result['error_message'] = " vge_id is empty";
            return $result;
        }
        /*
         * retrieve CaseType id for Huuropzegging
         */
        try {
            $case_type_api = civicrm_api3('OptionValue', 'Get', array('option_group_id' => 26));
            if (isset($case_type_api['values'])) {
                foreach($case_type_api as $case_type) {
                    if ($case_type['name'] == "Dossier Huuropzegging") {
                        $case_type_id = $case_type['value'];
                    }
                }
                if (!$case_type_id || empty($case_type_id)) {
                    $result['is_error'] = 1;
                    $result['error message'] = "No case type Dossier Huuropzegging found";
                    return $result;
                }
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving case_type_id for Dossier 
                Huuropzegging with OptionValue API. Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * retrieve custom group vge that extends case for found case type
         */
        $api_params = array(
            'name'                          =>  "vge",
            'extends'                       =>  "Case",
            'extends_entity_column_value'   =>  $case_type_id
        );
        try {
            $custom_group_api = civicrm_api3('CustomGroup', 'Getsingle', $api_params);
            if (isset($custom_group_api['id'])) {
                $custom_group_id = $custom_group_api['id'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group vge found";
                return $result;
            }
            if (isset($custom_group_api['table_name'])) {
                $custom_group_table = $custom_group_api['table_name'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group table name found";
                return $result;
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving custom_group_id for vge with CustomGroup API. 
                Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * read records in custom group where entity_id = vge_id
         */
        $custom_query = "SELECT * FROM $custom_group_table WHERE entity_id = {$this->vge_id}";
        $dao_vge = CRM_Core_DAO::executeQuery($custom_query);
        while ($dao_vge->fetch()) {
            /*
             * add every custom field to array
             */
            $update_fields = array();
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("complex_id", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $complex_id = CRM_Core_DAO::escapeString($this->complex_id);
                $update_fields[] = $custom_field['column_name']." = '$complex_id'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_straat", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_straat = CRM_Core_DAO::escapeString($this->vge_street_name);
                $update_fields[] = $custom_field['column_name']." = '$vge_straat'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_huis_nr", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $update_fields[] = $custom_field['column_name']." = '".$this->vge_street_number."'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_suffix", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_suffix = CRM_Core_DAO::escapeString($this->vge_street_unit);
                $update_fields[] = $custom_field['column_name']." = '$vge_suffix'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_adres", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_adres = CRM_Core_DAO::escapeString($this->_formatVgeAdres());
                $update_fields[] = $custom_field['column_name']." = '$vge_adres'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_postcode", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_postcode = CRM_Core_DAO::escapeString($this->vge_postal_code);
                $update_fields[] = $custom_field['column_name']." = '$vge_postcode'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_plaats", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_plaats = CRM_Core_DAO::escapeString($this->vge_city);
                $update_fields[] = $custom_field['column_name']." = '$vge_plaats'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_nr", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $update_fields[] = $custom_field['column_name']." = '".$this->vge_id."'";
            }
            /*
             * if elements in update_fields then update record
             */
            if (!empty($update_fields)) {
                $update_query = "UPDATE $custom_group_table SET ".implode(", ".$update_fields)." WHERE id = $dao_vge->id";
                CRM_Core_DAO::executeQuery($update_query);
            }
        }       
        /*
         * retrieve custom group woningwaardering that extends case for found case type
         */
        $api_params = array(
            'name'                          =>  "woningwaardering",
            'extends'                       =>  "Case",
            'extends_entity_column_value'   =>  $case_type_id
        );
        try {
            $custom_group_api = civicrm_api3('CustomGroup', 'Getsingle', $api_params);
            if (isset($custom_group_api['id'])) {
                $custom_group_id = $custom_group_api['id'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group woningwaardering found";
                return $result;
            }
            if (isset($custom_group_api['table_name'])) {
                $custom_group_table = $custom_group_api['table_name'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group table name found";
                return $result;
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving custom_group_id for woningwaardering with CustomGroup API. 
                Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * read records in custom group where entity_id = vge_id
         */
        $custom_query = "SELECT * FROM $custom_group_table WHERE entity_id = {$this->vge_id}";
        $dao_woningwaardering = CRM_Core_DAO::executeQuery($custom_query);
        while ($dao_woningwaardering->fetch()) {
            /*
             * add every custom field to array
             */
            $update_fields = array();
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("epa_label_opzegging", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $epa_label = CRM_Core_DAO::escapeString($this->epa_label);
                $update_fields[] = $custom_field['column_name']." = '$epa_label'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("epa_pre_opzegging", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $epa_pre = CRM_Core_DAO::escapeString($this->epa_pre);
                $update_fields[] = $custom_field['column_name']." = '$epa_pre'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("woningoppervlakte", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $update_fields[] = $custom_field['column_name']." = '".$this->square_mtrs."'";
            }
            /*
             * if elements in update_fields then update record
             */
            if (!empty($update_fields)) {
                $update_query = "UPDATE $custom_group_table SET ".implode(", ".$update_fields)." WHERE id = $dao_woningwaardering->id";
                CRM_Core_DAO::executeQuery($update_query);
            }
        }       
    }
    /**
     * private function to glue formatted address
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 9 Jan 2014
     * @return string $result
     */
    private function _formatVgeAdres() {
        $formatted_address = array();
        if (!empty($this->vge_street_name)) {
            $formatted_address[] = $this->vge_street_name;
        }
        if (!empty($this->vge_street_number)) {
            $formatted_address[] = $this->vge_street_number;
        }
        if (!empty($this->vge_street_unit)) {
            $formatted_address = $this->vge_street_unit;
        }
        $result = implode(" ", $formatted_address);
        if (!empty($this->vge_postal_code)) {
            $result .= ", ".$this->vge_postal_code;
            if (!empty($this->vge_city)) {
                $result .= " ".$this->vge_city;
            }
        } else {
            if (!empty($this->vge_city)) {
                $result .= ", ".$this->vge_city;
            }
        }
        return $result;
    }
    /**
     * private function to create a property type
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 15 Jan 2014
     * 
     * @param string $vge_type
     * @return boolean TRUE/FALSE
     */
    private function _createPropertyType($vge_type) {
        if (empty($vge_type)) {
            return FALSE;
        }
        $query = "INSERT INTO ".$this->_type_table." SET label = '$vge_type'";
        CRM_Core_DAO::executeQuery($query);
        $latest_query = "SELECT MAX(id) AS max_id FROM ".$this->_type_table;        
        $dao_latest = CRM_Core_DAO::executeQuery($latest_query);
        if ($dao_latest->fetch()) {
            if (isset($dao_latest->max_id)) {
                $this->_vge_type_id = $dao_latest->max_id;
            }
        }
        return TRUE;
    }
    /**
     * public function to check if there is a property with the vge_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 15 Jan 2014
     * @param integer $vge_id
     * @return TRUE or FALSE
     */
    public function checkVgeIdExists($vge_id) {
        if (empty($vge_id) || !is_numeric($vge_id)) {
            return FALSE;
        }
        $query = "SELECT COUNT(*), id AS count_property FROM ".$this->_table." WHERE vge_id = $vge_id";
        $dao = CRM_Core_DAO::executeQuery($query);
        if ($dao->fetch()) {
            if ($dao->count_property > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }
    /**
     * public function to set the id of the property based on vge_id
     * @param integer $vge_id
     */
    public function setIdWithVgeId($vge_id) {
        if (!empty($vge_id) && is_numeric($vge_id)) {
            $query = "SELECT id FROM ".$this->_table." WHERE vge_id = $vge_id";
            $dao = CRM_Core_DAO::executeQuery($query);
            if ($dao->fetch()) {
                $this->id = $dao->id;
            }
        }
    }
}
