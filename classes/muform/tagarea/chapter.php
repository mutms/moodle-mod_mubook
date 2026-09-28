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

namespace mod_mubook\muform\tagarea;

use core\context;

/**
 * Tags of a book chapter.
 *
 * @package    mod_mubook
 * @copyright  2026 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class chapter extends \tool_mulib\muform\tagarea\base {
    /** @var context\module book context */
    private context\module $context;

    /**
     * Constructor.
     *
     * @param int $mubookid
     * @param int|null $chapterid null for new chapters
     */
    public function __construct(
        /** @var int book id */
        private readonly int $mubookid,
        /** @var int|null chapter id */
        private readonly ?int $chapterid
    ) {
        global $DB;
        $mubook = $DB->get_record('mubook', ['id' => $mubookid], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('mubook', $mubook->id, $mubook->course, false, MUST_EXIST);
        $this->context = context\module::instance($cm->id);
        require_capability('mod/mubook:editchapter', $this->context);
        if ($chapterid) {
            $DB->get_record('mubook_chapter', ['id' => $chapterid, 'mubookid' => $mubookid], 'id', MUST_EXIST);
        }
    }

    #[\Override]
    public function get_args(): array {
        return [$this->mubookid, $this->chapterid];
    }

    #[\Override]
    public function get_component(): string {
        return 'mod_mubook';
    }

    #[\Override]
    public function get_itemtype(): string {
        return 'mubook_chapter';
    }

    #[\Override]
    public function get_context(): context {
        return $this->context;
    }

    #[\Override]
    public function get_itemid(): ?int {
        return $this->chapterid;
    }
}
