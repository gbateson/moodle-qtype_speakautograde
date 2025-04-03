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

    $newversion = 2019032302;
    if ($oldversion < $newversion) {
        // Add fields for Poodll recording.
        $fieldnames = 'timelimit, language, expiredays, transcode, transcriber, audioskin, videoskin';
        xmldb_qtype_speakautograde_addfields($dbman, $pluginoptionstable, $fieldnames);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2021100580;
    if ($oldversion < $newversion) {
        // Fields for compatability with qtype_essay in Moodle >= 3.10
        // and more granular matching of entries in the Glossary of common errors
        $fieldnames = array(
            'minwordlimit', 'maxwordlimit', 'maxbytes',
            'errorfullmatch', 'errorcasesensitive', 'errorignorebreaks'
        );
        xmldb_qtype_speakautograde_addfields($dbman, $pluginoptionstable, $fieldnames);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2023122781;
    if ($oldversion < $newversion) {
        // Add "allowsimilarity" column to denote maximum allowable level of similarity,
        // and reorder other fields to match essayautograde question type.
        xmldb_qtype_speakautograde_addfields($dbman, $pluginoptionstable);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2025030483;
    if ($oldversion < $newversion) {
        // Add AI fields: "aiassistant" and "aipercent".
        xmldb_qtype_speakautograde_addfields($dbman, $pluginoptionstable, 'aiassistant, aipercent');
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

    $newversion = 2025040387;
    if ($oldversion < $newversion) {
        // Unify db fields settings in the DB with those in the XML schema (install.xml). 
        xmldb_qtype_speakautograde_addfields($dbman, $pluginoptionstable);
        upgrade_plugin_savepoint(true, $newversion, $plugintype, $pluginname);
    }

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

        // Fields that are inherited from the "Essay" question type.
        // We omit "id" and "questionid" because they are indexed fields and therefore hard to update.
        // We include "allowsimilarity", because it relates to the template and sample.
        new xmldb_field('responseformat', XMLDB_TYPE_CHAR, 16, null, XMLDB_NOTNULL, null, 'editor'),
        new xmldb_field('responserequired', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 1),
        new xmldb_field('responsefieldlines', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 15),
        new xmldb_field('minwordlimit', XMLDB_TYPE_INTEGER, 10),
        new xmldb_field('maxwordlimit', XMLDB_TYPE_INTEGER, 10),
        new xmldb_field('attachments', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('attachmentsrequired', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('graderinfo', XMLDB_TYPE_TEXT),
        new xmldb_field('graderinfoformat', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        // AI fields added in Moodle 4.5.
        new xmldb_field('aiassistant', XMLDB_TYPE_CHAR,  255, null, XMLDB_NOTNULL),
        new xmldb_field('aipercent', XMLDB_TYPE_INTEGER, 6, null, XMLDB_NOTNULL, null, 0),
        // Note: graderinfo is used as prompt for the AI assistant
        new xmldb_field('responsetemplate', XMLDB_TYPE_TEXT),
        new xmldb_field('responsetemplateformat', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('responsesample', XMLDB_TYPE_TEXT),
        new xmldb_field('responsesampleformat', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('allowsimilarity', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 10),
        new xmldb_field('maxbytes', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('filetypeslist', XMLDB_TYPE_TEXT),

        // Fields that are specific to the "Essay (auto-grade)" question type.
        new xmldb_field('enableautograde', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 1),
        new xmldb_field('itemtype', XMLDB_TYPE_INTEGER, 4, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('itemcount', XMLDB_TYPE_INTEGER, 6, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showfeedback', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showcalculation', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showtextstats', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('textstatitems', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL),
        new xmldb_field('showgradebands', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('addpartialgrades', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('showtargetphrases', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),

        // Common errors
        new xmldb_field('errorcmid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorpercent', XMLDB_TYPE_INTEGER, 6, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorfullmatch', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorcasesensitive', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('errorignorebreaks', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),

        // Poodll recording
        new xmldb_field('timelimit', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('language', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL),
        new xmldb_field('expiredays', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 100),
        new xmldb_field('transcode', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('transcriber', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null, 'chrome'),
        new xmldb_field('audioskin', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null, 'onetwothree'),
        new xmldb_field('videoskin', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null, 'onetwothree'),

        // Standard feedback
        new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT),
        new xmldb_field('correctfeedbackformat', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT),
        new xmldb_field('incorrectfeedbackformat', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0),
        new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT),
        new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, 2, null, XMLDB_NOTNULL, null, 0)
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
