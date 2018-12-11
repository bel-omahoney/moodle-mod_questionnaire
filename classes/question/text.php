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
 * This file contains the parent class for text question types.
 *
 * @author Mike Churchward
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questiontypes
 */

namespace mod_questionnaire\question;
defined('MOODLE_INTERNAL') || die();

class text extends base {

    /**
     * Constructor. Use to set any default properties.
     *
     */
    public function __construct($id = 0, $question = null, $context = null, $params = []) {
        $this->length = 20;
        $this->precise = 25;
        return parent::__construct($id, $question, $context, $params);
    }

    /**
     * @return object|string
     */
    protected function responseclass() {
        return '\\mod_questionnaire\\response\\text';
    }

    /**
     * @return string
     */
    public function helpname() {
        return 'textbox';
    }

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function question_template() {
        return 'mod_questionnaire/question_text';
    }

    /**
     * Override and return a response template if provided. Output of response_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function response_template() {
        return 'mod_questionnaire/response_text';
    }

    /**
     * Return the context tags for the check question template.
     * @param object $data
     * @param string $descendantdata
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($data, $descendantsdata, $blankquestionnaire=false) {
        // Text Box.
        $questiontags = new \stdClass();
        $questiontags->qelements = new \stdClass();
        $choice = new \stdClass();
        $choice->onkeypress = 'return event.keyCode != 13;';
        $choice->size = $this->length;
        $choice->name = 'q'.$this->id;
        if ($this->precise > 0) {
            $choice->maxlength = $this->precise;
        }
        $choice->value = (isset($data->{'q'.$this->id}) ? stripslashes($data->{'q'.$this->id}) : '');
        $choice->id = self::qtypename($this->type_id) . $this->id;
        $questiontags->qelements->choice = $choice;
        return $questiontags;
    }

    /**
     * Return the context tags for the text response template.
     * @param object $data
     * @return object The radio question response context tags.
     *
     */
    protected function response_survey_display($data) {
        $resptags = new \stdClass();
        if (isset($data->{'q'.$this->id})) {
            $resptags->content = format_text($data->{'q'.$this->id}, FORMAT_HTML);
        }
        return $resptags;
    }

    /**
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
    protected function form_length(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_length($mform, 'fieldlength');
    }

    /**
     * @param \MoodleQuickForm $mform
     * @param string $helptext
     */
    protected function form_precise(\MoodleQuickForm $mform, $helptext = '') {
        return parent::form_precise($mform, 'maxtextlength');
    }

    /**
     * True if question provides mobile support.
     *
     * @return bool
     */
    public function supports_mobile() {
        return true;
    }

    /**
     * @param $qnum
     * @param $fieldkey
     * @param bool $autonum
     * @return \stdClass
     * @throws \coding_exception
     */
    public function get_mobile_question_data($qnum, $fieldkey, $autonum = false) {
        $mobiledata = parent::get_mobile_question_data($qnum, $fieldkey, $autonum = false);
        $mobiledata->questionsinfo['istextessay'] = true;
        return $mobiledata;
    }

    /**
     * @param $mobiledata
     * @return mixed
     */
    public function add_mobile_question_choice_data($mobiledata) {
        $mobiledata->questions = [];
        $mobiledata->questions[0] = new \stdClass();
        $mobiledata->questions[0]->id = 0;
        $mobiledata->questions[0]->choice_id = 0;
        $mobiledata->questions[0]->question_id = $this->id;
        $mobiledata->questions[0]->content = '';
        $mobiledata->questions[0]->value = null;
        return $mobiledata;
    }

    /**
     * @param $rid
     * @return \stdClass
     */
    public function get_mobile_response_data($rid) {
        $results = $this->get_results($rid);
        $resultdata = new \stdClass();
        $resultdata->answered = false;
        $resultdata->questions = [];
        $resultdata->responses = '';
        if (!empty($results) && $this->has_choices()) {
            $resultdata->answered = true;
            foreach ($results as $result) {
                $resultdata->questions[0]->value = $result->response;
                $resultdata->responses = $result->response;
            }
        }

        return $resultdata;
    }
}