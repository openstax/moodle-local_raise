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
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
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
     * Utility function to query / set a user UUID for RAISE
     *
     * @return string A new or existing user UUID
     */
    public static function get_or_create_jwt($uuid) {

        $cache = \cache::make('local_raise', 'userdata');

        $keyid = get_config('local_raise', 'KEY_ID');
        $keysecret = get_config('local_raise', 'KEY_SECRET');

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
            $decoded = JWT::decode($data, new Key($keysecret, 'HS256'));
            $decodedarray = json_decode(json_encode($decoded), true);
            $exp = $decodedarray['exp'];

            if ( time() < $exp - 12 * 60 * 60) {
                return $data;
            }
        }

        $payload = [
            "sub"  => $uuid,
            "exp"  => time() + 24 * 60 * 60
        ];

        $jwt = JWT::encode($payload, $keysecret, 'HS256', $keyid);

        $cache->set('jwt', $jwt);

        return $jwt;

    }
    public static function get_or_create_user_uuid() {
        global $USER, $DB;

        $cache = \cache::make('local_raise', 'userdata');

        $data = $cache->get($USER->id);

        if ($data) {
            return $data['user_uuid'];
        }

        $raiseuser = $DB->get_record(
            'local_raise_user',
            array('user_id' => $USER->id),
            'user_uuid',
            IGNORE_MISSING
        );

        if ($raiseuser) {
            $cache->set($USER->id, array('user_uuid' => $raiseuser->user_uuid));
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
}
