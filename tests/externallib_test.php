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

use externallib_advanced_testcase;
use local_get_raise_user_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/local/raise/component/external/get_raise_user.php');

/**
 * RAISE Ajax Service tests
 *
 * @package     local_raise
 * @copyright   2022 OpenStax
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_raise_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test test_get_raise_user_testcase
     */
    public function test_get_raise_user_testcase() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_course();
        $this->setUser($user);

        $startsize = $DB->count_records('local_raise_user');

        $result = local_get_raise_user_external::get_raise_user();
        $result = \external_api::clean_returnvalue(local_get_raise_user_external::get_raise_user_returns(), $result);

        $userdata = $DB->get_record(
            'local_raise_user',
            array('user_id' => $USER->id),
            'user_uuid',
            IGNORE_MISSING
        );

        $endsize = $DB->count_records('local_raise_user');

        $this->assertEquals($endsize, $startsize + 1);
        $this->assertEquals($result['uuid'], $userdata->user_uuid);

        $result = local_get_raise_user_external::get_raise_user();
        $result = \external_api::clean_returnvalue(local_get_raise_user_external::get_raise_user_returns(), $result);

        $finalsize = $DB->count_records('local_raise_user');

        $this->assertEquals($endsize, $finalsize);
    }
}
