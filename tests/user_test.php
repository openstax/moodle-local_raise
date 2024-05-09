<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
namespace local_raise;
use \local_raise\external\user;
use externallib_advanced_testcase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * RAISE Ajax Service tests
 *
 * @package     local_raise
 * @copyright   2022 OpenStax
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_test extends externallib_advanced_testcase {

    /**
     * Test test_get_raise_user_testcase
     *
     * @covers \local_raise\external\user::get_raise_user
     */
    public function test_get_user_service() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_course();
        $this->setUser($user);
        set_config('tokenkeyid', '1234', 'local_raise');
        set_config('tokenkeysecret', '1234', 'local_raise');
        $startsize = $DB->count_records('local_raise_user');

        $result = user::get_raise_user();
        $result = \external_api::clean_returnvalue(user::get_raise_user_returns(), $result);

        $userdata = $DB->get_record(
            'local_raise_user',
            ['user_id' => $USER->id],
            'user_uuid',
            IGNORE_MISSING
        );

        $endsize = $DB->count_records('local_raise_user');
        $this->assertEquals($endsize, $startsize + 1);
        $this->assertEquals($result['uuid'], $userdata->user_uuid);

        // If jwt::decode causes an exception it means the $result['jwt'] is invalid or expired.
        $decoded = JWT::decode($result['jwt'], new Key('1234', 'HS256'));
        $this->assertEquals($decoded->sub, $result['uuid']);
        $this->assertNotNull($decoded->exp, "JWT exp is null");

        $result = user::get_raise_user();
        $result = \external_api::clean_returnvalue(user::get_raise_user_returns(), $result);

        $finalsize = $DB->count_records('local_raise_user');

        $this->assertEquals($endsize, $finalsize);
    }

     /**
      * Test test_get_raise_user_roles_testcase
      *
      * @covers \local_raise\external\user::get_raise_user_roles
      */
    public function test_get_raise_user_roles() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $course = $this->getDataGenerator()->create_course();
        $roleid = $this->getDataGenerator()->create_role(['shortname' => 'roleshortname']);
        $userrole = $DB->get_record('role', ['shortname' => 'roleshortname']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $userrole->id, 'manual');
        $context = \context_course::instance($course->id, MUST_EXIST);

        $roletable = $DB->get_record('role', ['id' => $roleid], 'shortname', MUST_EXIST);

        $result = user::get_raise_user_roles($course->id);
        $result = \external_api::clean_returnvalue(user::get_raise_user_roles_returns(), $result);

        $this->assertEquals($result[0], $roletable->shortname);
    }
}
