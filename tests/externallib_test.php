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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/local/raise/externallib.php');

/**
 * RAISE Ajax Service tests
 *
 * @package     local_raise
 * @copyright   2022 OpenStax
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_raise_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test test_get_raise_user_data_testcase
     */
    public function test_get_raise_user_data_testcase() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_course();
        $this->setUser($user);

        $start_len = $DB->count_records('local_raise_user_data_table');

        $result = local_raise_external::get_raise_user_data();
        $result = external_api::clean_returnvalue(local_raise_external::get_raise_user_data_returns(), $result);

        $research_id = $DB->get_record(
            'local_raise_user_data_table',
            array('user_id'=>$USER->id),
            '*',
            IGNORE_MISSING
        );

        $end_len = $DB->count_records('local_raise_user_data_table');

        $this->assertEquals($end_len, $start_len + 1);
        $this->assertEquals($result['uuid'], $research_id->research_uuid);

        $result = local_raise_external::get_raise_user_data();
        $result = external_api::clean_returnvalue(local_raise_external::get_raise_user_data_returns(), $result);

        $final_len = $DB->count_records('local_raise_user_data_table');

        $this->assertEquals($end_len, $final_len);
    }
}
 