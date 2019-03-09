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
$plugin->maturity  = MATURITY_STABLE;
$plugin->requires  = 2015111600; // Moodle 3.0
$plugin->version   = 2019030966;
$plugin->release   = '2019-03-10 (00)';

// https://docs.moodle.org/dev/Releases
// Moodle 3.6 2018120300  3 Dec 2018
// Moodle 3.5 2018051700 17 May 2018
// Moodle 3.4 2017111300 13 Nov 2017
// Moodle 3.3 2017051500 15 May 2017
// Moodle 3.2 2016120500  5 Dec 2016
// Moodle 3.1 2016052300 23 May 2016
// Moodle 3.0 2015111600 16 Nov 2015
// Moodle 2.9 2015051100 11 May 2015
