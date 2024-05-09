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
use external_multiple_structure;

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
            "jwt" => $jwt
        ];
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
     * Returns description of get_raise_user_roles() parameters
     *
     * @return external_function_parameters
     */
    public static function get_raise_user_roles_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course id'),
            ]
        );
    }

    /**
     * Get list of roles for an authenticated user for a specific course.
     *
     * @param int $courseid
     * @return array of roles associated with the authenticated user
     */
    public static function get_raise_user_roles($courseid) {
        global $DB;
        global $USER;

        $params = self::validate_parameters(
            self::get_raise_user_roles_parameters(),
            ['courseid' => $courseid]
        );

        $roles = $DB->get_records_sql(
            'SELECT role_type.shortname
             FROM {context} ctx
             INNER JOIN {role_assignments} role_asgmts ON ctx.id = role_asgmts.contextid
             INNER JOIN {role} role_type ON role_asgmts.roleid = role_type.id
             WHERE ctx.instanceid = :instanceid AND ctx.contextlevel = :contextlevel AND role_asgmts.userid = :userid',
                [
                    'instanceid' => $params['courseid'],
                    'contextlevel' => CONTEXT_COURSE,
                    'userid' => $USER->id
                ]);

        $result = [];
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $result[] = $role->shortname;
            };
        };
        return $result;
    }

    /**
     * Returns description of get_raise_user_roles return values
     *
     * @return external_description
     */
    public static function get_raise_user_roles_returns() {
        return new external_multiple_structure(
            new external_value(PARAM_TEXT, 'Roles associated with user')
        );
    }
}
