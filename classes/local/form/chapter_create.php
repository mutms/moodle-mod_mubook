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

use mod_mubook\muform\tagarea\chapter as chapter_tagarea;
use tool_mulib\muform\element\buttons;
use tool_mulib\muform\element\cancel;
use tool_mulib\muform\element\select;
use tool_mulib\muform\element\submit;
use tool_mulib\muform\element\tags;
use tool_mulib\muform\element\text;
use tool_mulib\muform\form;
use tool_mulib\muform\util\options;

/**
 * Create a new chapter.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class chapter_create extends form {
    #[\Override]
    protected function definition(): void {
        /** @var \mod_mubook\local\toc $toc */
        $toc = $this->get_extra_data()['toc'];
        $subchapter = $this->get_extra_data()['subchapter'];
        $position = $this->get_extra_data()['position'];
        $fromcreatechapterid = $this->get_extra_data()['fromcreatechapterid'];
        $mubook = $toc->get_mubook();
        $context = $toc->get_context();

        $topchapters = [];
        $subchapters = [];
        foreach ($toc->get_chapters() as $ch) {
            if ($ch->parentid) {
                $subchapters[$ch->parentid][$ch->id] = $ch;
            } else {
                $topchapters[$ch->id] = $ch;
            }
        }

        if ($topchapters) {
            // Always use chapter numbers here.
            if ($subchapter) {
                $afteroptions = new options();
                $positions = [];
                if ($fromcreatechapterid > 0) {
                    $from = $toc->get_chapter($fromcreatechapterid);
                    if ($from && isset($topchapters[$from->id])) {
                        // Restrict to creation in one parent chapter only,
                        // this must be the viewchapter page.
                        $topchapters = [$from->id => $topchapters[$from->id]];
                    }
                }
                foreach ($topchapters as $chapter) {
                    $optgroup = $toc->get_numbered_chapter_title($chapter->id);
                    $groupoptions = [$chapter->id => get_string('subchapter_position_first', 'mod_mubook', $optgroup)];
                    $positions[] = $chapter->id;
                    if (isset($subchapters[$chapter->id])) {
                        foreach ($subchapters[$chapter->id] as $subchapter) {
                            $option = $toc->get_numbered_chapter_title($subchapter->id);
                            $groupoptions[$subchapter->id] = get_string('subchapter_position_after', 'mod_mubook', $option);
                            $positions[] = $subchapter->id;
                        }
                    }
                    $afteroptions->add_optgroup($optgroup, $groupoptions);
                }

                $select = new select('position', get_string('subchapter_position', 'mod_mubook'), $afteroptions);
                if (in_array($position, $positions)) {
                    $select->set_default((string)$position);
                } else if (in_array($toc->get_last_chapter()->id, $positions)) {
                    $select->set_default((string)$toc->get_last_chapter()->id);
                } else if ($fromcreatechapterid > 0 && in_array($fromcreatechapterid, $positions)) {
                    $select->set_default((string)$fromcreatechapterid);
                }
                $select->set_required(true);
                $this->add($select);
            } else {
                $afteroptions = [
                    0 => get_string('chapter_position_first', 'mod_mubook'),
                ];
                foreach ($topchapters as $chapter) {
                    $option = $toc->get_numbered_chapter_title($chapter->id);
                    $afteroptions[$chapter->id] = get_string('chapter_position_after', 'mod_mubook', $option);
                }
                $select = new select('position', get_string('chapter_position', 'mod_mubook'), $afteroptions);
                if (isset($afteroptions[$position])) {
                    $select->set_default((string)$position);
                } else {
                    $select->set_default((string)array_key_last($afteroptions));
                }
                $select->set_required(true);
                $this->add($select);
            }
        }

        if ($this->get_extra_data()['subchapter']) {
            $title = new text('title', get_string('subchapter_title', 'mod_mubook'), ['maxlength' => 1333]);
        } else {
            $title = new text('title', get_string('chapter_title', 'mod_mubook'), ['maxlength' => 1333]);
        }
        $title->set_required(true);
        $this->add($title);

        $this->add(new tags('tags', get_string('tags'), new chapter_tagarea($mubook->id, null)));

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
        $options[''] = get_string('none');
        $contentcreate = new select('contentcreate', get_string('content_create', 'mod_mubook'), $options);
        if (isset($options[$mubook->contentdefault])) {
            $contentcreate->set_default($mubook->contentdefault);
        }
        $this->add($contentcreate);

        $this->add(new buttons('buttons'));
        if ($this->get_extra_data()['subchapter']) {
            $this->add(new submit('submit', get_string('subchapter_create', 'mod_mubook')), 'buttons');
        } else {
            $this->add(new submit('submit', get_string('chapter_create', 'mod_mubook')), 'buttons');
        }
        $this->add(new cancel(), 'buttons');
    }
}
