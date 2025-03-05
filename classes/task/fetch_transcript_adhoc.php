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
 * A qtype_speakautograde adhoc task
 *
 * @package    qtype_speakautograde
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_speakautograde\task;

defined('MOODLE_INTERNAL') || die();

use \qtype_speakautograde\cloudpoodll\constants;
use \qtype_speakautograde\cloudpoodll\utils;

// Get the required libraries.
require_once($CFG->dirroot.'/question/type/speakautograde/question.php');
require_once($CFG->dirroot.'/question/engine/questionattemptstep.php');
require_once($CFG->dirroot.'/question/engine/questionattempt.php');
require_once($CFG->dirroot.'/question/behaviour/manualgraded/behaviour.php');

/**
 * A mod_readaloud adhoc task to fetch back transcriptions from Amazon S3
 *
 * @package    qtype_speakautograde
 * @since      Moodle 2.7
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch_transcript_adhoc extends \core\task\adhoc_task {

    /**
     * Executes the ad-hoc task to fetch a transcript for an audio/video file.
     * Retrieves the transcript from an external service (Amazon/Google),
     * processes it, and updates the relevant Moodle question attempt.
     *
     * @throws \file_exception If the transcript retrieval fails permanently.
     */
    public function execute() {
        global $DB;
        $trace = new \text_progress_trace();
        $cd_string = $this->get_custom_data_as_string();
        
        // Deserialize custom data.
        $cd = unserialize($cd_string);

        // Fetch transcript and VTT data.
        $transcript = utils::fetch_transcript($cd->audiourl);
        $vtt = utils::fetch_vtt($cd->audiourl);

        if ($transcript === false) {
            // If the task has been running too long, mark as failed permanently.
            if ($cd->taskcreationtime + (HOURSECS * 24) < time()) {
                $msg = 'No transcript could be found';
                $this->do_forever_fail($msg, $trace);
                return;
            } else {
                // Otherwise, retry the task soon.
                $msg = 'Transcript appears to not be ready yet';
                $this->do_retry_soon($msg, $trace, $cd, $cd_string);
                return;
            }
        } else {
            // Successfully retrieved transcript, process and store it.
            $qa = $cd->qa;
            $oldstep = $cd->oldstep;
            $stepdata = array('answertranscriptx' => $transcript, 'answervttx' => $vtt);
            
            // Process the retrieved transcript.
            $qa->process_action($stepdata, time(), $oldstep->get_user_id(), $oldstep->get_id());
            $qa->finish(time(), $oldstep->get_id());
        }
    }

    /**
     * Retries the task soon if the transcript is not yet available.
     * If the task has been running for a short period, it will be queued
     * for the next cron run. Otherwise, it will be delayed before retrying.
     *
     * @param string $reason The reason for retrying.
     * @param \text_progress_trace $trace The trace object for logging output.
     * @param object $customdata The custom data associated with the task.
     * @param string $customdata_string Serialized string of the custom data.
     */
    protected function do_retry_soon($reason, $trace, $customdata, $customdata_string) {
        if ($customdata->taskcreationtime + (MINSECS * 15) < time()) {
            $this->do_retry_delayed($reason, $trace);
        } else {
            $msg = $reason . ': will try again next cron ';
            $trace->output($msg);
            
            // Requeue the task for next execution.
            $fetch_task = new \qtype_speakautograde\task\fetch_transcript_adhoc();
            $fetch_task->set_component('qtype_speakautograde');
            $fetch_task->set_custom_data_as_string($customdata_string);
            \core\task\manager::queue_adhoc_task($fetch_task);
        }
    }

    /**
     * Delays the task retry if the transcript cannot be fetched immediately.
     * Throws an exception to indicate that transcript retrieval failed temporarily.
     *
     * @param string $reason The reason for the retry delay.
     * @param \text_progress_trace $trace The trace object for logging output.
     * @throws \file_exception If the transcript could not be fetched.
     */
    protected function do_retry_delayed($reason, $trace) {
        $msg = $reason . ': will retry after a delay ';
        $trace->output($msg);
        throw new \file_exception('retrievefileproblem', 'could not fetch transcript.');
    }

    /**
     * Marks the task as permanently failed and does not retry.
     *
     * @param string $reason The reason for failure.
     * @param \text_progress_trace $trace The trace object for logging output.
     */
    protected function do_forever_fail($reason, $trace) {
        $msg = $reason . ': will not retry ';
        $trace->output($msg);
    }
}
