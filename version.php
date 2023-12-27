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
 * Version information for the speakautograde question type.
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  2005 Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->cron      = 0;
$plugin->component = 'qtype_speakautograde';
$plugin->dependencies = array('qtype_essayautograde' => ANY_VERSION);
$plugin->maturity  = MATURITY_STABLE;
$plugin->requires  = 2015111600; // Moodle 2.9
$plugin->version   = 2023122781;
$plugin->release   = '2023-12-27 (81)';

// https://docs.moodle.org/dev/Releases
// Moodle 3.11 2021051700 17 May 2021
// Moodle 3.10 2020110900  9 Nov 2020
// Moodle 3.9  2020061500 15 Jun 2020
// Moodle 3.8  2019111800 18 Nov 2019
// Moodle 3.7  2019052000 20 May 2019
// Moodle 3.6  2018120300  3 Dec 2018
// Moodle 3.5  2018051700 17 May 2018
// Moodle 3.4  2017111300 13 Nov 2017
// Moodle 3.3  2017051500 15 May 2017
// Moodle 3.2  2016120500  5 Dec 2016
// Moodle 3.1  2016052300 23 May 2016
// Moodle 3.0  2015111600 16 Nov 2015
// Moodle 2.9  2015051100 11 May 2015
