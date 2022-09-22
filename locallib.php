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

    $researchId = $DB->get_record(
        'local_raise_user_data_table',
        array('user_id' => $USER->id),
        '*',
        IGNORE_MISSING
    );

    if ($researchId) {
        $uuid = $researchId->research_uuid;
    } else {
        $uuid = \core\uuid::generate();
        $researchIdentifier = new stdClass();
        $researchIdentifier->user_id = $USER->id;
        $researchIdentifier->research_uuid = $uuid;
        $DB->insert_record('local_raise_user_data_table', $researchIdentifier);
    }

    return $uuid;
}
