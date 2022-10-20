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
            array()
        );
    }
    public function has_config() {
        return true;
    }
    /**
     * Describes an endpoint to collect user parameters
     *
     * @return array user uuid
     */
    public static function get_raise_user() {
        $params = self::validate_parameters(
            self::get_raise_user_parameters(),
            array()
        );

        $uuid = \local_raise\user_helper::get_or_create_user_uuid();
        $jwt = \local_raise\user_helper::get_or_create_jwt($uuid);

        return array(
            "uuid"  => $uuid,
            "jwt"   => $jwt
        );
    }


    /**
     * Returns description of get_raise_use return values
     *
     * @return external_single_structure
     */
    public static function get_raise_user_returns() {
        return new external_single_structure(
            array(
                "uuid" => new external_value(PARAM_TEXT, 'Unique RAISE user identifier'),
                "jwt" => new external_value(PARAM_TEXT, 'JSON web tokeb')

            ));
    }
}
