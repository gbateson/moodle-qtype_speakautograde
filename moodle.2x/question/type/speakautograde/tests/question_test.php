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
 * Unit tests for the speakautograde question definition class.
 *
 * @package    qtype
 * @subpackage speakautograde
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/speakautograde/tests/helper.php');

/**
 * Unit tests for the matching question definition class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_speakautograde_question_test extends advanced_testcase {
    public function test_get_question_summary() {
        $speakautograde = qtype_speakautograde_test_helper::make_an_speakautograde_question();
        $speakautograde->questiontext = 'Hello <img src="http://example.com/globe.png" alt="world" />';
        $this->assertEquals('Hello [world]', $speakautograde->get_question_summary());
    }

    public function test_summarise_response() {
        $longstring = str_repeat('0123456789', 50);
        $speakautograde = qtype_speakautograde_test_helper::make_an_speakautograde_question();
        $this->assertEquals($longstring, $speakautograde->summarise_response(
                array('answer' => $longstring, 'answerformat' => FORMAT_HTML)));
    }

    public function test_is_same_response() {
        $speakautograde = qtype_speakautograde_test_helper::make_an_speakautograde_question();

        $speakautograde->responsetemplate = '';
        $speakautograde->responsesample = '';

        $speakautograde->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($speakautograde->is_same_response(
                array(),
                array('answer' => '')));

        $this->assertTrue($speakautograde->is_same_response(
                array('answer' => ''),
                array('answer' => '')));

        $this->assertTrue($speakautograde->is_same_response(
                array('answer' => ''),
                array()));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => 'Hello'),
                array()));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => 'Hello'),
                array('answer' => '')));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => 0),
                array('answer' => '')));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => ''),
                array('answer' => 0)));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => '0'),
                array('answer' => '')));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => ''),
                array('answer' => '0')));
    }

    public function test_is_same_response_with_template() {
        $this->test_is_same_response_with_editor('responsetemplate');
    }

    public function test_is_same_response_with_sample() {
        $this->test_is_same_response_with_editor('responsesample');
    }

    public function test_is_same_response_with_editor($editorfieldname) {
        $speakautograde = qtype_speakautograde_test_helper::make_an_speakautograde_question();

        $speakautograde->$editorfieldname = $editortext;

        $speakautograde->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($speakautograde->is_same_response(
                array(),
                array('answer' => 'Once upon a time')));

        $this->assertTrue($speakautograde->is_same_response(
                array('answer' => ''),
                array('answer' => 'Once upon a time')));

        $this->assertTrue($speakautograde->is_same_response(
                array('answer' => 'Once upon a time'),
                array('answer' => '')));

        $this->assertTrue($speakautograde->is_same_response(
                array('answer' => ''),
                array()));

        $this->assertTrue($speakautograde->is_same_response(
                array('answer' => 'Once upon a time'),
                array()));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => 0),
                array('answer' => '')));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => ''),
                array('answer' => 0)));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => '0'),
                array('answer' => '')));

        $this->assertFalse($speakautograde->is_same_response(
                array('answer' => ''),
                array('answer' => '0')));
    }

    public function test_is_complete_response() {
        $this->resetAfterTest(true);

        // Create a new logged-in user, so we can test responses with attachments.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create sample attachments to use in testing.
        $helper = test_question_maker::get_test_helper('speakautograde');
        $attachments = array();
        for ($i = 0; $i < 4; ++$i) {
            $attachments[$i] = $helper->make_attachments_saver($i);
        }

        // Create the speakautograde question under test.
        $speakautograde = qtype_speakautograde_test_helper::make_an_speakautograde_question();
        $speakautograde->start_attempt(new question_attempt_step(), 1);

        // Test the "traditional" case, where we must recieve a response from the user.
        $speakautograde->responserequired = 1;
        $speakautograde->attachmentsrequired = 0;
        $speakautograde->responseformat = 'editor';

        // The empty string should be considered an incomplete response, as should a lack of a response.
        $this->assertFalse($speakautograde->is_complete_response(array('answer' => '')));
        $this->assertFalse($speakautograde->is_complete_response(array()));

        // Any nonempty string should be considered a complete response.
        $this->assertTrue($speakautograde->is_complete_response(array('answer' => 'A student response.')));
        $this->assertTrue($speakautograde->is_complete_response(array('answer' => '0 times.')));
        $this->assertTrue($speakautograde->is_complete_response(array('answer' => '0')));

        // Test the case where two files are required.
        $speakautograde->attachmentsrequired = 2;

        // Attaching less than two files should result in an incomplete response.
        $this->assertFalse($speakautograde->is_complete_response(array('answer' => 'A')));
        $this->assertFalse($speakautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[0])));
        $this->assertFalse($speakautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[1])));

        // Anything without response text should result in an incomplete response.
        $this->assertFalse($speakautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[2])));

        // Attaching two or more files should result in a complete response.
        $this->assertTrue($speakautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[2])));
        $this->assertTrue($speakautograde->is_complete_response(
                array('answer' => 'A', 'attachments' => $attachments[3])));

        // Test the case in which two files are required, but the inline
        // response is optional.
        $speakautograde->responserequired = 0;

        $this->assertFalse($speakautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[1])));

        $this->assertTrue($speakautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[2])));

        // Test the case in which both the response and inline text are optional.
        $speakautograde->attachmentsrequired = 0;

        // Providing no answer and no attachment should result in an incomplete
        // response.
        $this->assertFalse($speakautograde->is_complete_response(
                array('answer' => '')));
        $this->assertFalse($speakautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[0])));

        // Providing an answer _or_ an attachment should result in a complete
        // response.
        $this->assertTrue($speakautograde->is_complete_response(
                array('answer' => '', 'attachments' => $attachments[1])));
        $this->assertTrue($speakautograde->is_complete_response(
                array('answer' => 'Answer text.', 'attachments' => $attachments[0])));

        // Test the case in which we're in "no inline response" mode,
        // in which the response is not required (as it's not provided).
        $speakautograde->reponserequired = 0;
        $speakautograde->responseformat = 'noinline';
        $speakautograde->attachmensrequired = 1;

        $this->assertFalse($speakautograde->is_complete_response(
                array()));
        $this->assertFalse($speakautograde->is_complete_response(
                array('attachments' => $attachments[0])));

        // Providing an attachment should result in a complete response.
        $this->assertTrue($speakautograde->is_complete_response(
                array('attachments' => $attachments[1])));

        // Ensure that responserequired is ignored when we're in inline response mode.
        $speakautograde->reponserequired = 1;
        $this->assertTrue($speakautograde->is_complete_response(
                array('attachments' => $attachments[1])));

    }

}
