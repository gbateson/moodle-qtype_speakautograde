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
 * Speak question type upgrade code.
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the speakautograde question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_speakautograde_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    $plugintype = 'qtype';
    $pluginname = 'speakautograde';
    $plugin = $plugintype.'_'.$pluginname;
    $pluginoptionstable = $plugin.'_options';

    return true;
}

/**
 * Upgrade code for the speakautograde question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_speakautograde_addfields($dbman, $pluginoptionstable, $fieldnames=null) {

    static $allfieldsadded = false;

    if ($allfieldsadded) {
        return true;
    }

    if ($fieldnames===null) {
        $allfieldsadded = true;
    }

    if (is_string($fieldnames)) {
        $fieldnames = explode(',', $fieldnames);
        $fieldnames = array_map('trim', $fieldnames);
        $fieldnames = array_filter($fieldnames);
    }

    $table = new xmldb_table($pluginoptionstable);
    $fields = array(
        new xmldb_field('responsesample',                 XMLDB_TYPE_TEXT),
        new xmldb_field('responsesampleformat',           XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('filetypeslist',                  XMLDB_TYPE_TEXT),
        new xmldb_field('enableautograde',                XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 1),
        new xmldb_field('itemtype',                       XMLDB_TYPE_INTEGER,  4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('itemcount',                      XMLDB_TYPE_INTEGER,  6, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showfeedback',                   XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showcalculation',                XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showtextstats',                  XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('textstatitems',                  XMLDB_TYPE_CHAR,   255, null, XMLDB_NOTNULL),
        new xmldb_field('showgradebands',                 XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('addpartialgrades',               XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showtargetphrases',              XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorcmid',                      XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorpercent',                   XMLDB_TYPE_INTEGER,  6, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('correctfeedback',                XMLDB_TYPE_TEXT),
        new xmldb_field('correctfeedbackformat',          XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('incorrectfeedback',              XMLDB_TYPE_TEXT),
        new xmldb_field('incorrectfeedbackformat',        XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('partiallycorrectfeedback',       XMLDB_TYPE_TEXT),
        new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER,  2, null, XMLDB_NOTNULL, null, 0)
    );

    $previousfield = 'responsetemplateformat';
    foreach ($fields as $field) {
        $currentfield = $field->getName();
        if ($fieldnames===null || in_array($currentfield, $fieldnames)) {
            if ($dbman->field_exists($table, $previousfield)) {
                $field->setPrevious($previousfield);
            }
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            } else {
                $dbman->add_field($table, $field);
            }
        }
        $previousfield = $currentfield;
    }
}
