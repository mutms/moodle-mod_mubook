<?php
// This file is part of MuTMS suite of plugins for Moodle™ LMS.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong

namespace mod_mubook\local\form;

use mod_mubook\muform\tagarea\chapter as chapter_tagarea;
use tool_mulib\muform\element\buttons;
use tool_mulib\muform\element\cancel;
use tool_mulib\muform\element\submit;
use tool_mulib\muform\element\tags;
use tool_mulib\muform\element\text;
use tool_mulib\muform\form;

/**
 * Update a chapter.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class chapter_update extends form {
    #[\Override]
    protected function definition(): void {
        $chapter = $this->get_extra_data()['chapter'];

        if ($chapter->parentid) {
            $title = new text('title', get_string('subchapter_title', 'mod_mubook'), ['maxlength' => 1333]);
        } else {
            $title = new text('title', get_string('chapter_title', 'mod_mubook'), ['maxlength' => 1333]);
        }
        $title->set_required(true);
        $this->add($title);

        $this->add(new tags('tags', get_string('tags'), new chapter_tagarea($chapter->mubookid, $chapter->id)));

        $this->add(new buttons('buttons'));
        if ($chapter->parentid) {
            $this->add(new submit('submit', get_string('subchapter_update', 'mod_mubook')), 'buttons');
        } else {
            $this->add(new submit('submit', get_string('chapter_update', 'mod_mubook')), 'buttons');
        }
        $this->add(new cancel(), 'buttons');
    }
}
