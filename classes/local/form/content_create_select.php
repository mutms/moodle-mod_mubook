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
// phpcs:disable moodle.Commenting.InlineComment.DocBlock

namespace mod_mubook\local\form;

use tool_mulib\muform\element\buttons;
use tool_mulib\muform\element\cancel;
use tool_mulib\muform\element\info;
use tool_mulib\muform\element\select;
use tool_mulib\muform\element\submit;
use tool_mulib\muform\form;

/**
 * Content type selection for creation.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class content_create_select extends form {
    #[\Override]
    protected function definition(): void {
        /** @var \mod_mubook\local\chapter $chapter */
        $chapter = $this->get_extra_data()['chapter'];
        /** @var \mod_mubook\local\toc $toc */
        $toc = $this->get_extra_data()['toc'];
        $mubook = $toc->get_mubook();
        $context = $toc->get_context();

        $title = $toc->get_numbered_chapter_title($chapter->id);
        if ($chapter->parentid) {
            $this->add(new info('statictitle', get_string('subchapter_title', 'mod_mubook'), $title, info::PLAIN));
        } else {
            $this->add(new info('statictitle', get_string('chapter_title', 'mod_mubook'), $title, info::PLAIN));
        }

        $options = [];
        $cman = \core\di::get(\mod_mubook\local\content_manager::class);
        /**
         * @var string $type
         * @var class-string<\mod_mubook\local\content> $classname
         */
        foreach ($cman->get_available_classes() as $type => $classname) {
            if ($classname::can_create(null, $mubook, $context)) {
                $options[$type] = $classname::get_name();
            }
        }
        \core_collator::asort($options);
        $type = new select('type', get_string('content_create', 'mod_mubook'), $options);
        $type->set_required(true);
        if (isset($options[$mubook->contentdefault])) {
            $type->set_default($mubook->contentdefault);
        }
        $this->add($type);

        $this->add(new buttons('buttons'));
        $this->add(new submit('submit', get_string('continue')), 'buttons');
        $this->add(new cancel(), 'buttons');
    }
}
