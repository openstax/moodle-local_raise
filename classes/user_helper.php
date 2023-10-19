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
 * RAISE user utilities
 *
 * @package    local_raise
 * @copyright  2022 OpenStax
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_raise;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;

/**
 * RAISE user utilities
 *
 * @package    local_raise
 * @copyright  2022 OpenStax
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_helper {

    /**
     * Utility function to get or create JWT
     * @param string $uuid
     * @return string JWT
     */
    public static function get_or_create_jwt($uuid) {

        $cache = \cache::make('local_raise', 'userdata');

        $keyid = get_config('local_raise', 'tokenkeyid');
        $keysecret = get_config('local_raise', 'tokenkeysecret');

        if (!$keyid || !$keysecret) {
            return null;
        }

        $data = $cache->get('jwt');
        $decoded = null;
        if ($data) {
            try {
                $decoded = JWT::decode($data, new Key($keysecret, 'HS256'));
            } catch (UnexpectedValueException $e) {
                // Expired jwt or Key and secret changed.
                $decoded = null;
            }
        }

        if ($decoded) {
            $exp = $decoded->exp;
            // Return cached token if it's valid for more than 12 hours.
            // Otherwise we'll proactively refresh.

            if (time() < $exp - 12 * 60 * 60) {
                return $data;
            }
        }

        $payload = [
            "sub" => $uuid,
            "exp" => time() + 24 * 60 * 60
        ];

        $jwt = JWT::encode($payload, $keysecret, 'HS256', $keyid);

        $cache->set('jwt', $jwt);

        return $jwt;

    }
    /**
     * Utility function to query / set a user UUID for RAISE
     *
     * @return string A new or existing user UUID
     */
    public static function get_or_create_user_uuid() {
        global $USER, $DB;

        $cache = \cache::make('local_raise', 'userdata');

        $data = $cache->get('uuid');

        if ($data) {
            return $data;
        }

        $raiseuser = $DB->get_record(
            'local_raise_user',
            ['user_id' => $USER->id],
            'user_uuid',
            IGNORE_MISSING
        );

        if ($raiseuser) {
            $cache->set('uuid', $raiseuser->user_uuid);

            return $raiseuser->user_uuid;
        }

        $uuid = \core\uuid::generate();
        $newraiseuser = new \stdClass();
        $newraiseuser->user_id = $USER->id;
        $newraiseuser->user_uuid = $uuid;
        $DB->insert_record('local_raise_user', $newraiseuser);
        $cache->set('uuid', $uuid);

        return $uuid;
    }

    /**
     * Utility function to get all user data from tool_policy_acceptance table
     *
     * @return array Array containing policy acceptance data (id, policyversionid, userid, status, name)
     */
    public static function get_policy_acceptance_data() {
        global $DB,  $USER;
    
        $sql = "SELECT pa.id, pa.policyversionid, pa.userid, pa.status, pv.name
                FROM {tool_policy_acceptance} pa
                JOIN {tool_policy_versions} pv ON pa.policyversionid = pv.id
                WHERE pa.userid = :userid";
    
        $params = ['userid' => $$USER->id];
        $policyAcceptanceData = $DB->get_records_sql($sql, $params);
    
        $data = [];
        foreach ($policyAcceptanceData as $record) {
            $data[] = [
                'id' => $record->id,
                'policyversionid' => $record->policyversionid,
                'userid' => $record->userid,
                'status' => $record->status,
                'name' => $record->name,
            ];
        }
    
        return $data;
    }
}    
