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
 * Classes to enforce the various access rules that can apply to a activity.
 *
 * @package    block_activity_results
 * @copyright  2009 Tim Hunt
 * @copyright  2015 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/lib.php');
class block_test_block extends block_base {

    public function init() {
        // initiate
    }

    public function has_config() {
        return true;
    }

    public function applicable_formats() {
        return array('course-view' => true);
    }

    public function get_content() {
        global $USER, $CFG, $DB;
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = '<h3>Moodle Test Block</h3><br/><br/>';

        $cid = $_REQUEST['id'];
        
        $getactivities = $DB->get_records_sql("SELECT * FROM mdl_course_modules WHERE course = $cid AND visible = 1");
        foreach($getactivities as $act)
        {
            $modname = $DB->get_record_sql("SELECT * FROM mdl_modules WHERE id =$act->module");
            $mod = $DB->get_record_sql("SELECT * FROM mdl_$modname->name WHERE id =$act->instance");
            $createdtime = date('d-M-Y', $act->added);;
            $chkcompletion = $DB->get_record_sql("SELECT * FROM `mdl_course_modules_completion` 
            WHERE coursemoduleid = $act->id AND userid = $USER->id AND completionstate >= $act->completion");
            if($chkcompletion)
            {
                $completed = '- Completed';
            }
            else
            {
                $completed = '';
            }
            $this->content->text .= '<a href='.$CFG->wwwroot.'/mod/'.$modname->name.'/view.php?id='.
            $act->id.'>'.$act->id.' - '.$mod->name.' - '.$createdtime.' '.$completed.'<br/></a>';
        }

        $this->content->footer = '';
        return $this->content;
    }
    
}
