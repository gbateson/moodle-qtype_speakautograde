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
 * Defines the editing form for the speakautograde question type.
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/question/type/essayautograde/edit_essayautograde_form.php');

/**
 * Speak question type editing form.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_edit_form extends qtype_essayautograde_edit_form {

    /**
     * Plugin name is class name without trailing "_edit_form"
     */
    public function plugin_name() {
        return substr(get_class($this), 0, -10);
    }

    protected function definition_inner($mform) {
        global $PAGE;

        parent::definition_inner($mform);

        $plugin = "qtype_speakautograde";
        $config = get_config($plugin);
        $qtype = question_bank::get_qtype('speakautograde');

        ////////////////////////////////////////////////
        /// Hide Essay response options
        /////////////////////////////////////////////////

        // The name of the element before which we want to insert all the options
        $before = 'responsetemplateheader';

        // The following fields do not really apply to Speak auto-grade questions,
        // so we replace them with hidden fields with a reasonable default value.
        $values = array('responserequired'    => 1,
                        'responsefieldlines'  => 5,
                        'attachments'         => 0,
                        'attachmentsrequired' => 0,
                        'filetypeslist'       => '',
                        // fields available on Moodle >= 3.11
                        //'minwordlimit'        => 0,
                        //'maxwordlimit'        => 0,
                        //'maxbytes'            => 0,
                        // fields available within "errorbehavior" group
                        'errorbehavior' => array(
                            'errorfullmatch'  => 1,
                            'errorcasesensitive' => 1,
                            'errorignorelinebreaks' => 1
                        ));

        foreach ($values as $name => $value) {
            if ($mform->elementExists($name)) {
                $mform->removeElement($name);
            }
            if (is_scalar($value)) {
                $mform->insertElementBefore($mform->createElement('hidden', $name, $value), $before);
                if (is_string($value)) {
                    $mform->setType($name, PARAM_TEXT);
                } else {
                    $mform->setType($name, PARAM_INT);
                }
            } else if (is_array($value)) {
                foreach ($value as $n => $v) {
                    $mform->insertElementBefore($mform->createElement('hidden', $n, $v), $before);
                    if (is_string($v)) {
                        $mform->setType($n, PARAM_TEXT);
                    } else {
                        $mform->setType($n, PARAM_INT);
                    }
                }
            }
        }

        ////////////////////////////////////////////////
        // Replace response format with Poodll options
        ////////////////////////////////////////////////

        $name = 'responseformat';
        if ($mform->elementExists($name)) {
            $mform->removeElement($name, false);
        }
        $label = get_string($name, $plugin);
        $options = $qtype->response_formats();
        $element = $mform->createElement('select', $name, $label, $options);
        $mform->insertElementBefore($element, $before);
        $mform->addHelpButton($name, $name, $plugin);

        ////////////////////////////////////////////////
        /// Add settings for Cloud Poodll media player
        /////////////////////////////////////////////////

        // timelimit
        $name = 'timelimit';
        $label = get_string($name, $plugin);
        $options = \qtype_speakautograde\cloudpoodll\utils::get_timelimit_options();
        $mform->insertElementBefore($mform->createElement('select', $name, $label, $options), $before);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, 60);

        // language options
        $name = 'language';
        $label = get_string($name, $plugin);
        $options = \qtype_speakautograde\cloudpoodll\utils::get_lang_options();
        $mform->insertElementBefore($mform->createElement('select', $name, $label, $options), $before);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $config->$name);

        // audioskin
        $name = 'audioskin';
        $label = get_string($name, $plugin);
        $type = \qtype_speakautograde\cloudpoodll\constants::REC_AUDIO;
        $options = \qtype_speakautograde\cloudpoodll\utils::fetch_options_skins($type);
        $mform->insertElementBefore($mform->createElement('select', $name, $label, $options), $before);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault('audioskin', $config->$name);

        // videoskin
        $name = 'videoskin';
        $label = get_string($name, $plugin);
        $type = \qtype_speakautograde\cloudpoodll\constants::REC_VIDEO;
        $options = \qtype_speakautograde\cloudpoodll\utils::fetch_options_skins($type);
        $mform->insertElementBefore($mform->createElement('select', $name, $label, $options), $before);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $config->$name);

        // transcriber
        $name = 'transcriber';
        $label = get_string($name, $plugin);
        $options = \qtype_speakautograde\cloudpoodll\utils::fetch_options_transcribers();
        $mform->insertElementBefore($mform->createElement('select', $name, $label, $options), $before);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $config->$name);

        // transcode
        $name = 'transcode';
        $label = get_string($name, $plugin);
        $text = get_string('transcode_details', $plugin);
        $mform->insertElementBefore($mform->createElement('advcheckbox', $name, $label, $text), $before);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $config->$name);

        // expiredays
        $name = 'expiredays';
        $label = get_string($name, $plugin);
        $options = \qtype_speakautograde\cloudpoodll\utils::get_expiredays_options();
        $mform->insertElementBefore($mform->createElement('select', $name, $label, $options), $before);
        $mform->addHelpButton($name, $name, $plugin);
        $mform->setDefault($name, $config->$name);
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        // process fields for Poodll plyer
        /*
        $question->timelimit = $question->options->timelimit;
        $question->language = $question->options->language;
        $question->expiredays = $question->options->expiredays;
        $question->transcode = $question->options->transcode;
        $question->transcriber = $question->options->transcriber;
        $question->audioskin = $question->options->audioskin;
        $question->videoskin = $question->options->videoskin;
        */

        return $question;
    }
}
