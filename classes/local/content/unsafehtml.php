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

namespace mod_mubook\local\content;

use mod_mubook\local\toc;
use stdClass;

/**
 * Content in unsafe raw HTML format.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class unsafehtml extends \mod_mubook\local\content {
    #[\Override]
    public static function is_unsafe(): bool {
        return true;
    }

    #[\Override]
    public function render(\renderer_base $output, toc $toc, int $firstheading): string {
        $context = $toc->get_context();

        $html = file_rewrite_pluginfile_urls($this->data1, 'pluginfile.php', $context->id, 'mod_mubook', 'content', $this->id);

        // Filtering may break raw HTML, skip it here.

        if ($this->unsafetrusted !== 1) {
            $html = clean_text($html, FORMAT_HTML);
        }

        return '<div class="mubook-format-html no-overflow">' . $html . '</div>';
    }

    #[\Override]
    public static function get_file_areas(): array {
        return ['content'];
    }

    #[\Override]
    public function send_file(string $fullpath, bool $forcedownload, array $options) {
        if ($this->unsafetrusted == 1) {
            if (!$forcedownload) {
                $options['dontforcesvgdownload'] = true;
            }
        } else {
            $forcedownload = true;
        }

        $fs = get_file_storage();

        $file = $fs->get_file_by_hash(sha1($fullpath));
        if (!$file || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, $forcedownload, $options);
    }
}
