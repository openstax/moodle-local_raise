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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds admin settings for the plugin.
 *
 * @package     local_raise
 * @category    admin
 * @copyright  2022 OpenStax
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
if ($hassiteconfig) {
    $ADMIN->add('admintools', new admin_category('jwt_config', get_string('jwt_config', 'local_raise')));
    $settingspage = new admin_settingpage('jwtkeys', get_string('jwt_config', 'local_raise'));

    if ($ADMIN->fulltree) {
        $settingspage->add(new admin_setting_configtext(
            'local_raise/KEY_ID',
            'local_raise KEY_ID',
            'Description',
            ''
        ));
        $settingspage->add(new admin_setting_configtext(
            'local_raise/KEY_SECRET',
            'local_raise/KEY_SECRET',
            'Description',
            ''
        ));

    }

    $ADMIN->add('localplugins', $settingspage);
}
