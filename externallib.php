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

/**
 * Plugin version and other metadata.
 *
 * @package    local_fe_events_direct
 * @copyright  2021 OpenStax
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . "/filelib.php");
require_once("$CFG->dirroot/local/raise/locallib.php");



/**
 * Front End Events Direct Web Service
 *
 * @package    local_raise_direct
 * @copyright  2021 OpenStax
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_raise_external extends external_api {

    /**
     * Returns description of user_id() parameters
     *
     * @return external_function_parameters
     */
    public static function get_raise_user_data_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Returns the UserId
     *
     * @return array userId
     */
    public static function get_raise_user_data(){
        $params = self::validate_parameters(
            self::get_raise_user_data_parameters(),
            array()
        );

        $uuid = get_or_create_research_id();
        return array(
            "uuid"  => $uuid,
        );
    }

    

    /**
     * Returns description of user_id() return values
     *
     * @return external_single_structure
     */
    public static function get_raise_user_data_returns() {
        return new external_single_structure(
            array(
                "uuid" => new external_value(PARAM_TEXT, 'User Research ID')
            ));
    }

}
