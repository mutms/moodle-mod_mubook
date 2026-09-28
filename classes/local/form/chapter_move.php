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
use tool_mulib\muform\element\checkbox;
use tool_mulib\muform\element\info;
use tool_mulib\muform\element\select;
use tool_mulib\muform\element\submit;
use tool_mulib\muform\form;
use tool_mulib\muform\util\options;

/**
 * Move a chapter.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class chapter_move extends form {
    #[\Override]
    protected function definition(): void {
        /** @var \mod_mubook\local\toc $toc */
        $toc = $this->get_extra_data()['toc'];
        $chapter = $this->get_extra_data()['chapter'];

        $firstchapter = $toc->get_first_chapter();
        $topchapters = [];
        $subchapters = [];
        foreach ($toc->get_chapters() as $ch) {
            if ($ch->parentid) {
                $subchapters[$ch->parentid][$ch->id] = $ch;
            } else {
                $topchapters[$ch->id] = $ch;
            }
        }

        $title = $toc->get_numbered_chapter_title($chapter->id);
        $this->add(new info('statictitle', get_string('chapter_title', 'mod_mubook'), $title, info::PLAIN));

        $showsubchapter = ($chapter->parentid || !isset($subchapters[$chapter->id]));
        if ($showsubchapter) {
            $subchapter = new checkbox('subchapter', get_string('subchapter', 'mod_mubook'));
            $subchapter->set_default((int)!empty($chapter->parentid));
            $this->add($subchapter);
        }

        $options = [];
        if (!$firstchapter || $firstchapter->id != $chapter->id) {
            $options[0] = get_string('chapter_position_first', 'mod_mubook');
        }
        foreach ($topchapters as $ch) {
            $title = $toc->get_numbered_chapter_title($ch->id);
            if ($ch->id == $chapter->id) {
                $options[$ch->id] = get_string('choosedots');
            } else {
                $options[$ch->id] = get_string('chapter_position_after', 'mod_mubook', $title);
            }
        }
        $positionchapter = new select('positionchapter', get_string('chapter_position', 'mod_mubook'), $options);
        if ($toc->is_orphaned_chapter($chapter->id)) {
            if ($topchapters) {
                $lasttopchapterid = array_key_last($topchapters);
                if (isset($options[$lasttopchapterid])) {
                    $positionchapter->set_default((string)$lasttopchapterid);
                }
            }
        } else if ($chapter->parentid) {
            if (isset($options[$chapter->parentid])) {
                $positionchapter->set_default((string)$chapter->parentid);
            }
        } else {
            if (isset($options[$chapter->id])) {
                $positionchapter->set_default((string)$chapter->id);
            }
        }
        $this->add($positionchapter);

        if ($showsubchapter) {
            $options = new options();
            $positions = [];
            foreach ($topchapters as $ch) {
                $positions[] = $ch->id;
                if ($ch->id == $chapter->id) {
                    $options->add_options([$ch->id => get_string('choosedots')]);
                    continue;
                }
                $optgroup = $toc->get_numbered_chapter_title($ch->id);
                $groupoptions = [$ch->id => get_string('subchapter_position_first', 'mod_mubook', $ch->format_title())];

                if (isset($subchapters[$ch->id])) {
                    foreach ($subchapters[$ch->id] as $subch) {
                        $positions[] = $subch->id;
                        if ($subch->id == $chapter->id) {
                            $groupoptions[$subch->id] = get_string('choosedots');
                            continue;
                        }
                        $title = $toc->get_numbered_chapter_title($subch->id);
                        $groupoptions[$subch->id] = get_string('subchapter_position_after', 'mod_mubook', $title);
                    }
                }
                $options->add_optgroup($optgroup, $groupoptions);
            }
            $positionsubchapter = new select('positionsubchapter', get_string('subchapter_position', 'mod_mubook'), $options);
            if (in_array($chapter->id, $positions)) {
                $positionsubchapter->set_default((string)$chapter->id);
            } else {
                $positionsubchapter->set_default((string)end($positions));
            }
            $this->add($positionsubchapter);

            $dm = $this->get_display_manager();
            $dm->hide_if('positionchapter', 'subchapter', 'checked');
            $dm->hide_if('positionsubchapter', 'subchapter', 'notchecked');
        }

        $this->add(new buttons('buttons'));
        if ($chapter->parentid) {
            $this->add(new submit('submit', get_string('subchapter_move', 'mod_mubook')), 'buttons');
        } else {
            $this->add(new submit('submit', get_string('chapter_move', 'mod_mubook')), 'buttons');
        }
        $this->add(new cancel(), 'buttons');
    }

    #[\Override]
    protected function validation(array $data, array &$allerrors): void {
        $chapter = $this->get_extra_data()['chapter'];

        if (!empty($data['subchapter'])) {
            if ($data['positionsubchapter'] == $chapter->id) {
                $allerrors['positionsubchapter'][] = get_string('required');
            }
        } else {
            if ($data['positionchapter'] == $chapter->id) {
                $allerrors['positionchapter'][] = get_string('required');
            }
        }
    }
}
