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

use tool_mulib\muform\element\buttons;
use tool_mulib\muform\element\cancel;
use tool_mulib\muform\element\info;
use tool_mulib\muform\element\submit;
use tool_mulib\muform\form;

/**
 * Delete chapter content instance.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class content_delete extends form {
    #[\Override]
    protected function definition(): void {
        /** @var \mod_mubook\local\content $content */
        $content = $this->get_extra_data()['content'];
        /** @var \mod_mubook\local\chapter $chapter */
        $chapter = $this->get_extra_data()['chapter'];
        /** @var \mod_mubook\local\toc $toc */
        $toc = $this->get_extra_data()['toc'];

        $title = $toc->get_numbered_chapter_title($chapter->id);
        if ($chapter->parentid) {
            $this->add(new info('statictitle', get_string('subchapter_title', 'mod_mubook'), $title, info::PLAIN));
        } else {
            $this->add(new info('statictitle', get_string('chapter_title', 'mod_mubook'), $title, info::PLAIN));
        }

        $this->add(new info('staticidentif', '', $content->get_identification(), info::PLAIN));

        $this->add(new buttons('buttons'));
        $this->add(new submit('submit', get_string('content_delete', 'mod_mubook')), 'buttons');
        $this->add(new cancel(), 'buttons');
    }
}
