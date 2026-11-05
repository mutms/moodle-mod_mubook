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
// phpcs:disable moodle.Commenting.DocblockDescription.Missing
// phpcs:disable moodle.Strings.ForbiddenStrings.Found

namespace mod_mubook\phpunit\local;

use mod_mubook\local\markdown_formatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Markdown formatter test.
 *
 * @package    mod_mubook
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\mod_mubook\local\markdown_formatter::class)]
#[Group('MuTMS')]
final class markdown_formatter_test extends \advanced_testcase {
    public function test_get_html_options(): void {
        $options = markdown_formatter::get_html_options();
        $this->assertSame(
            [markdown_formatter::HTML_STRIP, markdown_formatter::HTML_ESCAPE, markdown_formatter::HTML_ALLOW],
            array_keys($options)
        );
    }

    /**
     * Data provider for convert_to_html tests.
     * @return array[]
     */
    public static function dataprovider_convert_to_html(): array {
        return [
            [
                "# Main heading\n\nsome test",
                [],
                "<h1>Main heading</h1>\n<p>some test</p>\n",
            ],
            [
                "# Main heading\n\nsome test",
                ['firstheading' => 2],
                "<h2>Main heading</h2>\n<p>some test</p>\n",
            ],
            [
                "## Main heading\n\nsome test\n### Subheading",
                ['firstheading' => 3, 'headingoffset' => 1],
                "<h3 class=\"h4\">Main heading</h3>\n<p>some test</p>\n<h4 class=\"h5\">Subheading</h4>\n",
            ],
            [
                "![Image 1](logo.png)\n![Image 2](./logo.png)\n![Image 2](@@PLUGINFILE@@/logo.png)",
                [],
                '<p><img class="img-fluid" src="logo.png" alt="" />
<img class="img-fluid" src="./logo.png" alt="" />
<img class="img-fluid" src="@@PLUGINFILE@@/logo.png" alt="" /></p>
',
            ],
            [
                "![Image 1](logo.png)\n![Image 2](./logo.png)\n![Image 2](@@PLUGINFILE@@/logo.png)",
                ['filebase' => 'XX/'],
                '<p><img class="img-fluid" src="XX/logo.png" alt="" />
<img class="img-fluid" src="XX/logo.png" alt="" />
<img class="img-fluid" src="XX/logo.png" alt="" /></p>
',
            ],
            [
                "[File 1](Filefile.pdf)\n[File 2](./Filefile.pdf)\n[File 2](@@PLUGINFILE@@/Filefile.pdf)",
                [],
                "<p><a href=\"Filefile.pdf\">File 1</a>\n<a href=\"./Filefile.pdf\">File 2</a>\n<a href=\"@@PLUGINFILE@@/Filefile.pdf\">File 2</a></p>\n",
            ],
            [
                "[File 1](Filefile.pdf)\n[File 2](./Filefile.pdf)\n[File 2](@@PLUGINFILE@@/Filefile.pdf)",
                ['filebase' => 'XX/'],
                "<p><a href=\"XX/Filefile.pdf\">File 1</a>\n<a href=\"XX/Filefile.pdf\">File 2</a>\n<a href=\"XX/Filefile.pdf\">File 2</a></p>\n",
            ],
        ];
    }

    /**
     * @param string $markdown
     * @param array $options
     * @param string $html
     * @return void
     */
    #[DataProvider('dataprovider_convert_to_html')]
    public function test_convert_to_html(string $markdown, array $options, string $html): void {
        $this->assertSame($html, markdown_formatter::convert_to_html($markdown, $options));
    }

    /**
     * Data provider for tests of enabled Markdown features.
     * @return array[]
     */
    public static function dataprovider_features(): array {
        $html = "a <b>bold</b> <script>alert(1)</script>\n\n<div class=\"x\">div</div>";
        return [
            'html default is strip' => [
                $html,
                [],
                "<p>a bold alert(1)</p>\n\n",
            ],
            'html strip' => [
                $html,
                ['html' => markdown_formatter::HTML_STRIP],
                "<p>a bold alert(1)</p>\n\n",
            ],
            'html escape' => [
                $html,
                ['html' => markdown_formatter::HTML_ESCAPE],
                "<p>a &lt;b&gt;bold&lt;/b&gt; &lt;script&gt;alert(1)&lt;/script&gt;</p>\n&lt;div class=\"x\"&gt;div&lt;/div&gt;\n",
            ],
            'html allow' => [
                $html,
                ['html' => markdown_formatter::HTML_ALLOW],
                "<p>a <b>bold</b> &lt;script&gt;alert(1)&lt;/script&gt;</p>\n<div class=\"x\">div</div>\n",
            ],
            'html allow escapes disallowed raw html' => [
                "<iframe src=\"https://example.com\"></iframe>\n\n<textarea>x</textarea>\n\n<style>p{}</style>",
                ['html' => markdown_formatter::HTML_ALLOW],
                "&lt;iframe src=\"https://example.com\"&gt;&lt;/iframe&gt;\n&lt;textarea&gt;x&lt;/textarea&gt;\n&lt;style&gt;p{}&lt;/style&gt;\n",
            ],
            'unsafe links' => [
                "[a](javascript:alert(1)) [b](data:text/html,x) ![c](javascript:alert(1))",
                [],
                "<p><a>a</a> <a>b</a> </p>\n",
            ],
            'strikethrough' => [
                "~~gone~~ and ~one~",
                [],
                "<p><del>gone</del> and <del>one</del></p>\n",
            ],
            'table' => [
                "| A | B |\n|---|:-:|\n| 1 | 2 |",
                [],
                "<table>\n<thead>\n<tr>\n<th>A</th>\n<th align=\"center\">B</th>\n</tr>\n</thead>\n"
                    . "<tbody>\n<tr>\n<td>1</td>\n<td align=\"center\">2</td>\n</tr>\n</tbody>\n</table>\n",
            ],
            'alert note' => [
                "> [!NOTE]\n> Text.",
                [],
                "<div class=\"mubook-alert mubook-alert-note\">\n"
                    . "<p class=\"mubook-alert-title\"><i class=\"fa-solid fa-circle-info me-1\"></i>Note</p>\n<p>Text.</p>\n</div>\n",
            ],
            'alert tip' => [
                "> [!TIP]\n> Text.",
                [],
                "<div class=\"mubook-alert mubook-alert-tip\">\n"
                    . "<p class=\"mubook-alert-title\"><i class=\"fa-regular fa-lightbulb me-1\"></i>Tip</p>\n<p>Text.</p>\n</div>\n",
            ],
            'alert important' => [
                "> [!IMPORTANT]\n> Text.",
                [],
                "<div class=\"mubook-alert mubook-alert-important\">\n"
                    . "<p class=\"mubook-alert-title\"><i class=\"fa-solid fa-book-open-reader me-1\"></i>Important</p>\n<p>Text.</p>\n</div>\n",
            ],
            'alert warning' => [
                "> [!WARNING]\n> Text.",
                [],
                "<div class=\"mubook-alert mubook-alert-warning\">\n"
                    . "<p class=\"mubook-alert-title\"><i class=\"fa-solid fa-triangle-exclamation me-1\"></i>Warning</p>\n<p>Text.</p>\n</div>\n",
            ],
            'alert caution' => [
                "> [!CAUTION]\n> Text.",
                [],
                "<div class=\"mubook-alert mubook-alert-caution\">\n"
                    . "<p class=\"mubook-alert-title\"><i class=\"fa-solid fa-circle-exclamation me-1\"></i>Caution</p>\n<p>Text.</p>\n</div>\n",
            ],
            'blockquote' => [
                "> just quote",
                [],
                "<blockquote>\n<p>just quote</p>\n</blockquote>\n",
            ],
            'math inline' => [
                'Formula $`1+2`$ here',
                [],
                "<p>Formula \\(1+2\\) here</p>\n",
            ],
            'math block' => [
                "```math\nx^2\n```",
                [],
                "<div class=\"code-language-math\">\\(x^2\\)</div>\n",
            ],
            'code block' => [
                "```php\necho 1;\n```",
                [],
                "<pre><code class=\"language-php\">echo 1;\n</code></pre>\n",
            ],
            'task list' => [
                "- [x] done\n- [ ] todo",
                [],
                "<ul>\n<li><i class=\"fa-regular fa-square-check\" title=\"Task completed\"></i> done</li>\n"
                    . "<li><i class=\"fa-regular fa-square\" title=\"Task not completed\"></i> todo</li>\n</ul>\n",
            ],
            'no autolinks' => [
                "see https://example.com and www.example.com",
                [],
                "<p>see https://example.com and www.example.com</p>\n",
            ],
            'headings are normalised and capped' => [
                "###### deep\n\n# top",
                ['firstheading' => 5],
                "<h5>deep</h5>\n<h5>top</h5>\n",
            ],
            'firstheading max' => [
                "# h",
                ['firstheading' => 9],
                "<h6>h</h6>\n",
            ],
            'firstheading min' => [
                "# h",
                ['firstheading' => 0],
                "<h1>h</h1>\n",
            ],
            'headingoffset max' => [
                "# a\n## b",
                ['firstheading' => 5, 'headingoffset' => 3],
                "<h5 class=\"h6\">a</h5>\n<h6 class=\"h6\">b</h6>\n",
            ],
            'link title' => [
                "[x](https://example.com \"The title\")",
                [],
                "<p><a title=\"The title\" href=\"https://example.com\">x</a></p>\n",
            ],
            'filebase not applied to absolute links' => [
                "[a](https://example.com/x) [b](/abs/path) [c](#anchor) [d](mailto:a@example.com)",
                ['filebase' => 'XX/'],
                "<p><a href=\"https://example.com/x\">a</a> <a href=\"/abs/path\">b</a> <a href=\"#anchor\">c</a> <a href=\"mailto:a@example.com\">d</a></p>\n",
            ],
            'filebase not applied to absolute images' => [
                "![](https://example.com/x.png) ![](/abs.png)",
                ['filebase' => 'XX/'],
                "<p><img class=\"img-fluid\" src=\"https://example.com/x.png\" alt=\"\" /> <img class=\"img-fluid\" src=\"/abs.png\" alt=\"\" /></p>\n",
            ],
        ];
    }

    /**
     * @param string $markdown
     * @param array $options
     * @param string $html
     * @return void
     */
    #[DataProvider('dataprovider_features')]
    public function test_features(string $markdown, array $options, string $html): void {
        $this->assertSame($html, markdown_formatter::convert_to_html($markdown, $options));
    }

    public function test_max_nesting_level(): void {
        $html = markdown_formatter::convert_to_html(str_repeat('>', 30) . ' deep');
        $this->assertSame(20, substr_count($html, '<blockquote>'));
        $this->assertStringContainsString('<p>&gt;&gt;&gt;&gt;&gt;&gt;&gt;&gt;&gt;&gt; deep</p>', $html);
    }
}
