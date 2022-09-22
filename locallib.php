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
 * @package    local_raise
 * @copyright  2021 OpenStax
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function get_or_create_research_id() {
    global $USER, $DB;

    $research_id = $DB->get_record(
        'user_data_table',
        array('user_id'=>$USER->id),
        '*',
        IGNORE_MISSING
    );

    if ($research_id) {
        $uuid = $research_id->research_uuid;
    } else {
        // Create a new identifier for this user
        $uuid = \core\uuid::generate();
        $research_identifier = new stdClass();
        $research_identifier->user_id = $USER->id;
        $research_identifier->research_uuid = $uuid;
        $DB->insert_record('user_data_table', $research_identifier);
    }

    return $uuid;
}