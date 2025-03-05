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
 * Speak question definition class.
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// prevent direct access to this script
defined('MOODLE_INTERNAL') || die();

use qtype_speakautograde\cloudpoodll\constants;
use qtype_speakautograde\cloudpoodll\utils;

// require the parent class
require_once($CFG->dirroot.'/question/type/essayautograde/question.php');

/**
 * Represents an speakautograde question.
 *
 * We can use almost all the methods from the parent "qtype_essay_question" class.
 * However, we override "make_behaviour" in case automatic grading is required.
 * Additionally, we implement the methods required for automatic grading.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// interface: question_automatically_gradable
// class:     question_graded_automatically
class qtype_speakautograde_question extends qtype_essayautograde_question {

    /** @var int */
    public $timelimit;

    /** @var string */
    public $language;

    /** @var string */
    public $audioskin;

    /** @var string */
    public $videoskin;

    /** @var string */
    public $transcriber;

    /** @var int */
    public $transcode;

    /** @var int */
    public $expiredays;

    /** @return the result of applying {@link format_text()} to the question text. */
    public function format_questiontext($qa) {
        $qtext = parent::format_questiontext($qa);
        $template = question_utils::to_plain_text($this->responsetemplate,
                                                  $this->responsetemplateformat,
                                                  array('para' => false));
        $params = array('class' => 'audiovideo_response_prompt');
        $template = html_writer::tag('blockquote', $template, $params);
        return $qtext.$template;
    }

    public function update_current_response($response, $displayoptions=null) {
        //
        // update Poodll response here
        //
        if (!empty($response)){

            if(!empty($response['answeraudiourl']) && $response['answeraudiourl']!= null) {
                $this->save_current_response('answeraudiourl', $response['answeraudiourl']);

                // If we transcribed the audio on amazon, we pick it up now and poke it into the answer field.
                // We do not save it as "current_response", because in parent::update_current_response,
                // it looks at $response and overwrites current_response for the "answer" field.
                if($this->transcriber == constants::TRANSCRIBER_AMAZON_TRANSCRIBE) {
                    $answer = utils::fetch_transcript($response['answeraudiourl']);
                    if ($answer) {
                        $response['answer']=$answer;
                    }else{
                        $response['answer']='';
                    }
                }
            }

            if(!empty($response['answertranscript'])) {
                $this->save_current_response('answertranscript', $response['answertranscript']);
            }
        }
        parent::update_current_response($response, $displayoptions);

    }

    public function get_expected_data() {
        $expecteddata= parent::get_expected_data();
        $expecteddata['answertranscript'] = PARAM_RAW;
        $expecteddata['answeraudiourl'] = PARAM_URL;
        return $expecteddata;
    }

    public function is_complete_response(array $response) {
        // Determine if the given response has an audiourl
        // TODO add check for transcripts here
        $hasaudio = array_key_exists('answeraudiourl', $response) && ($response['answeraudiourl'] !== '');

        // The response is complete iff all of our requirements are met.
        return $hasaudio;
    }
}
