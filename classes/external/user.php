<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
namespace local_raise\external;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/externallib.php');
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

/**
 * RAISE Web Service Function - User Access Functions
 *
 * @package    local_raise
 * @copyright  2022 OpenStax
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends external_api {

    /**
     * Returns description of get_raise_user parameters
     *
     * @return external_function_parameters
     */
    public static function get_raise_user_parameters() {
        return new external_function_parameters(
            []
        );
    }

    /**
     * Returns description of get_policy_acceptance_data parameters
     *
     * @return external_function_parameters
     */
    public static function get_policy_acceptance_data_parameters() {
        return new external_function_parameters(
            []
        );
    }

    /**
     * Describes an endpoint to collect user parameters
     *
     * @return array user uuid
     */
    public static function get_raise_user() {
        $params = self::validate_parameters(
            self::get_raise_user_parameters(),
            []
        );

        $uuid = \local_raise\user_helper::get_or_create_user_uuid();
        $jwt = \local_raise\user_helper::get_or_create_jwt($uuid);

        return [
            "uuid" => $uuid,
            "jwt" => $jwt,
        ];
    }

    /**
     * Retrieve policy acceptance data for all users
     *
     * @return array Policy acceptance data (id, policyversionid, userid, status, name) for all users
     */
    public static function get_policy_acceptance_data() {
        $params = self::validate_parameters(
            self::get_policy_acceptance_data_parameters(),
            []
        );

        $policyAcceptanceData = \local_raise\user_helper::get_policy_acceptance_data();

        return $policyAcceptanceData;
    }


    /**
     * Returns description of get_raise_use return values
     *
     * @return external_single_structure
     */
    public static function get_raise_user_returns() {
        return new external_single_structure(
            [
                "uuid" => new external_value(PARAM_TEXT, 'Unique RAISE user identifier'),
                "jwt" => new external_value(PARAM_TEXT, 'JSON web token')
            ]
        );
    }

    /**
     * Returns description of get_policy_acceptance_data return values
     *
     * @return external_single_structure
     */
    public static function get_policy_acceptance_data_returns() {
        return new external_single_structure(
            [
                "id" => new external_value(PARAM_INT, 'Policy acceptance ID'),
                "policyversionid" => new external_value(PARAM_INT, 'Policy version ID'),
                "userid" => new external_value(PARAM_INT, 'User ID'),
                "status" => new external_value(PARAM_TEXT, 'Policy acceptance status'),
                "name" => new external_value(PARAM_TEXT, 'Policy name')
            ]
        );
    }
    
}
