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
 * Speak question renderer class.
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/essayautograde/renderer.php');

use qtype_speakautograde\cloudpoodll\utils;
use qtype_speakautograde\cloudpoodll\constants;

/**
 * Generates the output for speakautograde questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_renderer extends qtype_essayautograde_renderer {
    /**
     * Specify the short name for the editor used to input the response.
     * This is used to locate where on the page to insert the sample response.
     * For Essay questions, the editor type is "atto", "tinymce" or "textarea".
     * For Speak questions, the editor will be "audio" or "video".
     *
     * @param object $question
     * @return string The short name of the editor.
     */
    public function get_editor_type($question) {
        return $question->responseformat;
    }

    /**
     * Specify whether to show (TRUE) the item count DIV, or not (FALSE).
     *
     * @return bool
     */
    public function show_itemcount() {
        return false;
    }
}

/**
 * An speakautograde format renderer for speakautogrades where the student should not enter
 * any inline response.
 *
 * @copyright  2013 Binghamton University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_format_noinline_renderer extends qtype_essay_format_noinline_renderer {
    protected function class_name() {
        return 'qtype_speakautograde_noinline';
    }
}

/**
 * An speakautograde format renderer for speakautogrades where the student should use the HTML
 * editor without the file picker.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_format_editor_renderer extends qtype_essay_format_editor_renderer {
    protected function class_name() {
        return 'qtype_speakautograde_editor';
    }
}


/**
 * An speakautograde format renderer for speakautogrades where the student should use the HTML
 * editor with the file picker.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_format_editorfilepicker_renderer extends qtype_essay_format_editorfilepicker_renderer {
    protected function class_name() {
        return 'qtype_speakautograde_editorfilepicker';
    }
}


/**
 * An speakautograde format renderer for speakautogrades where the student should use a plain
 * input box, but with a normal, proportional font.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_format_plain_renderer extends qtype_essay_format_plain_renderer {
    protected function class_name() {
        return 'qtype_speakautograde_plain';
    }
}


/**
 * An speakautograde format renderer for speakautogrades where the student should use a plain
 * input box with a monospaced font. You might use this, for example, for a
 * question where the students should type computer code.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qtype_speakautograde_format_monospaced_renderer extends qtype_essay_format_plain_renderer {
    protected function class_name() {
        return 'qtype_speakautograde_monospaced';
    }
}

class qtype_speakautograde_format_base_renderer extends plugin_renderer_base{

    protected function class_name() {
        return 'qtype_speakautograde_base';
    }

    public function response_area_read_only($name, $qa, $step, $lines, $context) {
        $question = $qa->get_question();

        // Fetch submitted data. ($name = 'answer')
        $audiourl = $step->get_qt_var($name.'audiourl');
        $transcript = $step->get_qt_var($name.'transcript');

        // assume no subtitles
        $have_subtitles = false;

        // if Amazon transcribe we have subtitles, and would like to process grades again since transcripts arrive async
        if ($question->transcriber == constants::TRANSCRIBER_AMAZON_TRANSCRIBE) {
            $transcript = utils::fetch_transcript($audiourl);
            if ($transcript) {
                $have_subtitles = true;
                // We need a place to do this. Ideally, we should force a regrade
                // if a transcript had arrived recently but that doesn't work here
                // because we get "question already started" errors.
                $transcriptprocessed = $qa->get_metadata('transcriptprocessed');
                if (! $transcriptprocessed) {
                    $qa->set_metadata('transcriptprocessed', true);
                    //$qa->regrade($qa,false);
                }
            }
        }

        // transcript could be a url, or a block of text or empty
        // here we turn a url into text if we can
        if (empty($transcript)) {
            $transcript = get_string('transcriptnotready', CONSTANTS::M_COMPONENT);
            $have_subtitles = false;
        }

        $player_div = $this->fetch_player($audiourl, $question->language, $have_subtitles);

        if ($have_subtitles) {
            return $player_div;
        } else {
            return $player_div.html_writer::div($transcript, 'qtype_speakautograde_transcriptdiv', array());
        }

        // do this for testing fetch and process of transcript via ad hoc task.
        //utils::register_fetch_transcript_task($url, $qa, $step);
    }

    public function response_area_input($name, $qa, $step, $lines, $context) {
        $question = $qa->get_question();
        $fieldname = $qa->get_qt_field_name($name);

        // setup the recorder DIV
        $options = get_config('qtype_speakautograde');
        $recorder = $this->fetch_recorder($options, $question, $fieldname);

        // setup HIDDEN fields
        if (! $use_audiourl = $step->get_qt_var($name.'audiourl')) {
            $use_audiourl = '';
        }
        $audiourl = html_writer::empty_tag('input', array('type' => 'hidden',
                                                          'name' => $fieldname.'audiourl',
                                                          'value' => $use_audiourl));
        if (! $use_answer = $step->get_qt_var($name)) {
            $use_answer = constants::BLANK;
        }
        $answer = html_writer::empty_tag('input', array('type' => 'hidden',
                                                        'name' => $fieldname,
                                                        'value' => $use_answer));
        $format = html_writer::empty_tag('input', array('type' => 'hidden',
                                                        'name' => $fieldname.'format',
                                                        'value' => FORMAT_PLAIN));
        if (! $use_transcript = $step->get_qt_var($name.'transcript')) {
            $use_transcript = constants::BLANK;
        }
        $transcript = html_writer::empty_tag('input', array('type' => 'hidden',
                                                            'name' => $fieldname.'transcript',
                                                            'value' => $use_transcript));


        // return recorder and associated hidden fields
        return $recorder.$transcript.$audiourl.$answer.$format;
    }

    /**
     * @return string the HTML for the textarea.
     */
    protected function fetch_player($mediaurl, $language, $havesubtitles=false) {
        global $PAGE;

        $playerid = html_writer::random_id(constants::M_COMPONENT.'_');

        // define player template
        if ($this->class_name() == 'qtype_speakautograde_video') {
            // video player
            $player = "<video id='@PLAYERID@' crossorigin='anonymous' controls='true'><source src='@MEDIAURL@'>";
            if($havesubtitles){
                $player .= "<track src='@VTTURL@' kind='captions' srclang='@LANG@' label='@LANG@' default='true'>";
            }
            $player .= '</video>';
        } else {
            // audio player
            $player = "<audio id='@PLAYERID@' crossorigin='anonymous' controls='true'><source src='@MEDIAURL@'>";
            if($havesubtitles){
                $player .= "<track src='@VTTURL@' kind='captions' srclang='@LANG@' label='@LANG@' default='true'>";
            }
            $player .= '</audio>';
        }

        $player = str_replace('@PLAYERID@', $playerid, $player);
        $player = str_replace('@MEDIAURL@', $mediaurl, $player);
        $player = str_replace('@LANG@', $language, $player);
        $player = str_replace('@VTTURL@', $mediaurl.'.vtt',$player);

        // if we have subtitles add the transcript AMD and html
        if($havesubtitles) {
            $transcript_containerid = html_writer::random_id(constants::M_COMPONENT.'_');
            $transcript_container = html_writer::div('', constants::M_COMPONENT.'_transcriptcontainer', array('id' => $transcript_containerid));
            $player .= $transcript_container;

            // prepare AMD javascript for displaying transcript
            $opts = array('component' => constants::M_COMPONENT,
                          'playerid' => $playerid,
                          'containerid' => $transcript_containerid,
                          'cssprefix' => constants::M_COMPONENT.'_transcript');
            $PAGE->requires->js_call_amd(constants::M_COMPONENT.'/interactivetranscript', 'init', array($opts));
            $PAGE->requires->strings_for_js(array('transcripttitle'), constants::M_COMPONENT);
        }

        return $player;

    }



    /**
     * @return string the HTML for the textarea.
     */
    protected function fetch_recorder($r_options, $question, $inputname) {
        global $CFG;

        $width = '';
        $height = '';
        switch($this->class_name()) {

            case 'qtype_speakautograde_audio':
                $recordertype = constants::REC_AUDIO;
                $recorderskin = $question->audioskin;
                switch ($question->audioskin) {
                    case constants::SKIN_FRESH:
                        $width = '400';
                        $height = '300';
                        break;
                    case constants::SKIN_PLAIN:
                        $width = '360';
                        $height = '190';
                        break;
                    default:
                        // bmr 123, once, standard
                        $width = '360';
                        $height = '240';
                }
                break;

            case 'qtype_speakautograde_video':
            default:
                $recordertype = constants::REC_VIDEO;
                $recorderskin = $question->videoskin;
                switch ($question->videoskin) {
                    case constants::SKIN_BMR:
                        $width = '360';
                        $height = '450';
                        break;
                    case constants::SKIN_123:
                        $width = '450';
                        $height = '550';
                        break;
                    case constants::SKIN_ONCE:
                        $width = '350';
                        $height = '290';
                        break;
                    default:
                        $width = '360';
                        $height = '410';
                }
        }

        // amazon transcribe
        $transcriber = "chrome";
        if ($question->transcriber == constants::TRANSCRIBER_AMAZON_TRANSCRIBE) {
            $can_transcribe = utils::can_transcribe($r_options);
            $amazontranscribe = ($can_transcribe ? '1' : '0');
            $transcriber = "amazon";
        } else {
            $amazontranscribe = 0;
        }

        // chrometranscribe
        if ($question->transcriber == constants::TRANSCRIBER_CHROME) {
            $chrometranscribe = '1';
        } else {
            $chrometranscribe = 0;
        }

        // transcode
        $transcode = ($question->transcode  ? '1' : '0');

        // time limit
        $timelimit = $question->timelimit;

        // fetch cloudpoodll token
        $api_user = get_config(constants::M_COMPONENT, 'apiuser');
        $api_secret = get_config(constants::M_COMPONENT, 'apisecret');
        $token = utils::fetch_token($api_user, $api_secret);


        // any recorder hints ... go here..
        $hints = new \stdClass();
        $string_hints = base64_encode (json_encode($hints));

        // the elementid of the div in the DOM
        $dom_id = html_writer::random_id('');

        $recorderdiv = \html_writer::div('', constants::M_COMPONENT.'_notcenter',
            array('id' => $dom_id,
                'data-id' => 'therecorder_'.$dom_id,
                'data-parent' => $CFG->wwwroot,
                'data-localloader' => constants::LOADER_URL,
                'data-media' => $recordertype,
                'data-appid' => constants::APPID,
                'data-type' => $recorderskin,
                'data-width' => $width,
                'data-height' => $height,
                'data-updatecontrol' => $inputname,
                'data-timelimit' => $timelimit,
                'data-transcode' => $transcode,
                'data-transcribe' => $amazontranscribe,
                'data-subtitle' => $amazontranscribe,
                'data-speechevents' => $chrometranscribe,
                'data-language' => $question->language,
                'data-expiredays' => $question->expiredays,
                'data-region' => $r_options->awsregion,
                'data-fallback' => $r_options->fallback,
                'data-hints' => $string_hints,
                'data-token' => $token // localhost
                //'data-token' => '643eba92a1447ac0c6a882c85051461a' // cloudpoodll
            )
        );

        $containerdiv = \html_writer::div($recorderdiv, constants::CLASS_REC_CONTAINER.' ',
            array('id' => constants::CLASS_REC_CONTAINER.$dom_id));

        // this is the finalhtml
        $recorderhtml = \html_writer::div($containerdiv , constants::CLASS_REC_OUTER);

        // set up the AMD for the recorder
        $opts = array(
            'component' => constants::M_COMPONENT,
            'dom_id' => $dom_id,
            'inputname' => $inputname,
            'transcriber' => $transcriber
        );

        $this->page->requires->js_call_amd(constants::M_COMPONENT.'/cloudpoodllhelper', 'init', array($opts));
        //$PAGE->requires->strings_for_js(array('reallydeletesubmission'), constants::M_COMPONENT);

        return $recorderhtml;
    }
}

/**
 * An speakautograde format renderer for speakautogrades where the student should record audio.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_format_audio_renderer extends qtype_speakautograde_format_base_renderer {
    protected function class_name() {
        return 'qtype_speakautograde_audio';
    }
}

/**
 * An speakautograde format renderer for speakautogrades where the student should record audio.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_format_video_renderer extends qtype_speakautograde_format_base_renderer {
    protected function class_name() {
        return 'qtype_speakautograde_video';
    }
}
