<?php
/**
 * Created by PhpStorm.
 * User: tunbola.ogunwande
 * Date: 27/08/2016
 * Time: 11:44 PM
 */

/**
 * Fetch information about Personal Campaign Pages
 *
 * @param array $params Associative array of property name/value
 * for fetching information about personal campaign pages.
 * @return array api result array
 * @access public
 */
function civicrm_api3_p_c_p_get($params)
{
    return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * Fetch options for a  Personal Campaign Page field
 *
 * @param array $params Associative array of property name/value
 * for fetching options for a field
 * @return array api result array
 * @access public
 */

function civicrm_api3_p_c_p_getoptions($params)
{
    return civicrm_api3_generic_getoptions($params);
}