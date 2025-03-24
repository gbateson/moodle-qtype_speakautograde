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
 * Question type class for the speakautograde question type.
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  2005 Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/essayautograde/questiontype.php');

/**
 * The speakautograde question type.
 *
 * @copyright  2005 Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde extends qtype_essayautograde {

    /** Answer types in question_answers record */
    const ANSWER_TYPE_BAND    = 0;
    const ANSWER_TYPE_PHRASE  = 1;

    /** Item types */
    const ITEM_TYPE_NONE = 0;
    const ITEM_TYPE_CHARS = 1;
    const ITEM_TYPE_WORDS = 2;
    const ITEM_TYPE_SENTENCES = 3;
    const ITEM_TYPE_PARAGRAPHS = 4;

    /** Show/hide values */
    const SHOW_NONE                  = 0;
    const SHOW_STUDENTS_ONLY         = 1;
    const SHOW_TEACHERS_ONLY         = 2;
    const SHOW_TEACHERS_AND_STUDENTS = 3;

    public function extra_question_fields() {
        $fields = parent::extra_question_fields();
        // including "responseformat" and feedback fields.

        // add Poodll fields
        array_push($fields, 'timelimit', 'language',
                            'audioskin', 'videoskin',
                            'transcriber', 'transcode',
                            'expiredays');
        return $fields;
    }

    public function save_question_options($formdata) {
        global $DB;
        parent::save_question_options($formdata);

        // save Poodll options
        $plugin = $this->plugin_name();
        $optionstable = $plugin.'_options';

        if ($options = $DB->get_record($optionstable, array('questionid' => $formdata->id))) {
            $options->timelimit = $formdata->timelimit;
            $options->language = $formdata->language;
            $options->expiredays = $formdata->expiredays;
            $options->transcode = $formdata->transcode;
            $options->transcriber = $formdata->transcriber;
            $options->audioskin = $formdata->audioskin;
            $options->videoskin = $formdata->videoskin;
            return $DB->update_record($optionstable, $options);
        } else {
            return false; // no options - shouldn't happen !!
        }
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        //
        // Initialize Poodll fields here
        //
    }

    public function delete_question($questionid, $contextid) {
        //
        // Delete Poodll stuff here
        //
        parent::delete_question($questionid, $contextid);
    }

    public function response_formats() {
        $plugin = 'qtype_speakautograde';
        return array(
            'audio' => get_string('formataudio', $plugin),
            'video' => get_string('formatvideo', $plugin),
        );
    }

    /**
     * based on "regrade_attempt()" method
     * in "mod/quiz/report/overview/report.php"
     */
    protected function regrade_question($questionid) {
        //
        // Regrade Poodll stuff
        //
        parent::regrade_question($questionid);
    }

    /**
     * get_default_values
     *
     * @return array of default values for a new question
     */
    static public function get_default_values($questionid=0, $feedback=false) {    
        $values = parent::get_default_values($questionid, $feedback);
        $values = array_merge($values, [
            'timelimit'   =>  0, 'language'    => '',
            'audioskin'   => '', 'videoskin'   => '',
            'transcode'   =>  0, 'transcriber' => '',
            'expiredays'  =>  0,
        ]);
        return $values;
    }

    /**
     * Return a list of fields that must be present in a Speak (auto-grade) GIFT file.
     *
     * @return array of field names that are required in the GIFT file.
     */
    public function import_included_fields() {
        return ['timelimit', 'language',
                'audioskin', 'videoskin',
                'transcode', 'transcriber',
                'expiredays'];
    }

    /**
     * Return a list of fields that should not be present in a Speak (auto-grade) GIFT file.
     *
     * @return array of field names that are not allowed in the GIFT file.
     */
    public function import_excluded_fields() {
        return [];
    }
}
