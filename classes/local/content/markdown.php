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

namespace mod_mubook\local\content;

use mod_mubook\local\toc;
use mod_mubook\local\markdown_formatter;

/**
 * Content in Markdown format.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class markdown extends \mod_mubook\local\content {
    #[\Override]
    public function render(\renderer_base $output, toc $toc, int $firstheading): string {
        $mubook = $toc->get_mubook();
        $context = $toc->get_context();

        $filebase = $this->get_fileserving_base('content');
        $options = [
            'flavor' => $mubook->markdownflavor,
            'html' => $mubook->markdownhtml,
        ];
        $html = markdown_formatter::convert_to_html($this->record->data1, $firstheading, $filebase, $options);

        $formatoptions = [
            'noclean' => false,
            'para' => false,
            'context' => $context,
            'overflowdiv' => false,
        ];
        $html = format_text($html, FORMAT_HTML, $formatoptions);

        return '<div class="mubook-format-markdown">' . $html . '</div>';
    }

    #[\Override]
    public static function get_file_areas(): array {
        return ['content'];
    }
}
