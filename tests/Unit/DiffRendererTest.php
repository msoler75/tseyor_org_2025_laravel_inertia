<?php

namespace Tests\Unit;

use App\Helpers\DiffRenderer;
use Tests\TestCase;

class DiffRendererTest extends TestCase
{
    /** @test */
    public function it_detects_markdown_link_url_changes()
    {
        $old = '![undefined](/almacen/medios/comunicados/2005/62/image.png)';
        $new = '![undefined](/almacen/medios/logos/sello_tseyor_64.png)';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('undefined', $html);
        $this->assertStringContainsString('comunicados', $html);
        $this->assertStringContainsString('logos', $html);
    }

    /** @test */
    public function it_handles_url_diff_cleanly()
    {
        $old = '![undefined](/almacen/medios/comunicados/2005/62/image_1.png)';
        $new = '![undefined](/almacen/medios/guias/con_nombre/Shilcars.jpg?w=186)';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('undefined', $html);
        $this->assertStringContainsString('<del', $html);
        $this->assertStringContainsString('<ins', $html);
        $this->assertStringContainsString('comunicados', $html);
        $this->assertStringContainsString('guias', $html);
    }

    /** @test */
    public function it_handles_asterisk_to_underscore_change()
    {
        $old = '*de grandes conocimientos científicos, técnicos, filosóficos,*';
        $new = '_de grandes conocimientos científicos, técnicos, filosóficos,_';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString(
            'de grandes conocimientos científicos, técnicos, filosóficos,',
            strip_tags($html)
        );
        $this->assertStringContainsString('<del', $html);
        $this->assertStringContainsString('<ins', $html);
    }

    /** @test */
    public function it_handles_mixed_style_and_delimiter_change()
    {
        $old = '*que un sistema recurrente, una cinta sin fin,*';
        $new = '{style=text-align: center;}_que un sistema recurrente, una cinta sin fin,_';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString(
            'que un sistema recurrente, una cinta sin fin,',
            strip_tags($html)
        );
        $this->assertStringContainsString('{style=text-align: center;}', $html);
    }

    /** @test */
    public function it_handles_empty_old_value()
    {
        $old = '';
        $new = "New content\nwith\nlines";

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('New content', strip_tags($html));
        $this->assertStringContainsString('lines', strip_tags($html));
    }

    /** @test */
    public function it_handles_empty_new_value()
    {
        $old = "Old content\nwith\nlines";
        $new = '';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('Old content', strip_tags($html));
        $this->assertStringContainsString('lines', strip_tags($html));
    }

    /** @test */
    public function it_shows_no_changes_for_identical_text()
    {
        $old = 'Same text';
        $new = 'Same text';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('Sin cambios', $html);
    }

    /** @test */
    public function it_handles_multiline_diff_with_grouping()
    {
        $old = "line1\nline2\nline3\nline4";
        $new = "line1\nline2_changed\nline3_changed\nline4";

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('line1', $html);
        $this->assertStringContainsString('line4', $html);
    }

    /** @test */
    public function it_renders_single_line_word_diff()
    {
        $old = 'Hello world this is a test';
        $new = 'Hello beautiful world this is amazing';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('Hello', strip_tags($html));
        $this->assertStringContainsString('beautiful', strip_tags($html));
        $this->assertStringContainsString('amazing', strip_tags($html));
    }

    /** @test */
    public function it_handles_footnote_html_diff_cleanly()
    {
        $old = '...hablando<a name="footnote-1"></a>[<sup>1</sup>](#note-1).';
        $new = '...hablando[<sup>1</sup>](#note-1).';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('hablando', strip_tags($html));
        $this->assertStringContainsString('#note-1', strip_tags($html));
    }

    /** @test */
    public function it_handles_spaces_visibly_in_diff()
    {
        $old = '{style=text-align:center}';
        $new = '{style=text-align: center;}';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('text-align', $html);
        $this->assertStringContainsString('center', $html);
    }

    /** @test */
    public function url_diff_shows_segment_level_changes()
    {
        $old = '/almacen/medios/comunicados/2005/62/image.png';
        $new = '/almacen/medios/guias/con_nombre/Shilcars.jpg?w=186';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('/almacen/medios/', $html);
        $this->assertStringNotContainsString('char-by-char mess', $html);
    }

    /** @test */
    public function it_handles_real_revision_pair_1()
    {
        $old = <<<'MD'
![undefined](/almacen/medios/comunicados/2005/62/image.png)

{style=text-align: center;}

{style=text-align: center;}**CONVERSACIONES INTERDIMENSIONALES.**

{style=text-align: center;}**Periodo III Edición 00**
MD;
        $new = <<<'MD'
![undefined](/almacen/medios/logos/sello_tseyor_64.png)

{style=text-align: center;}

{style=text-align: center;}**CONVERSACIONES INTERDIMENSIONALES.**

{style=text-align: center;}**Periodo III Edición 00**
MD;

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('comunicados', $html);
        $this->assertStringContainsString('logos', $html);
    }

    /** @test */
    public function it_handles_real_revision_pair_3()
    {
        $old = <<<'MD'
**CONVERSACIONES INTERDIMENSIONALES.**

**Periodo III Edición 00**

**Núm. 45. Reunión de Puertas Abiertas**
MD;
        $new = <<<'MD'
![undefined](/almacen/medios/comunicados/2005/62/image.png)

{style=text-align: center;}

{style=text-align: center;}**CONVERSACIONES INTERDIMENSIONALES.**

{style=text-align: center;}**Periodo III Edición 00**

{style=text-align: center;}**Núm. 45. Reunión de Puertas Abiertas**
MD;

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('CONVERSACIONES INTERDIMENSIONALES', strip_tags($html));
        $this->assertStringContainsString('{style=text-align: center;}', $html);
    }

    /** @test */
    public function filename_with_underscore_is_not_split()
    {
        $old = 'image_1.png';
        $new = 'photo_2.jpg';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('image_1', $html);
        $this->assertStringContainsString('photo_2', $html);
    }

    /** @test */
    public function context_lines_are_limited()
    {
        $old = implode("\n", range(1, 30));
        $newLines = range(1, 30);
        $newLines[2] = 'changed_early';
        $newLines[25] = 'changed_late';
        $new = implode("\n", $newLines);

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('changed_early', $html);
        $this->assertStringContainsString('changed_late', $html);
        $this->assertStringContainsString('...', $html);
    }

    /** @test */
    public function it_handles_style_prefix_with_html_markup_change()
    {
        $old = '{style=text-align:center}**<u>79. DESIERTOS Y VERGELES</u>**';
        $new = '{style=text-align: center;}**79\\. DESIERTOS Y VERGELES**';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('DESIERTOS Y VERGELES', strip_tags($html));
        $this->assertStringContainsString('79', strip_tags($html));
    }

    /** @test */
    public function it_shows_full_blocks_for_completely_different_texts()
    {
        $old = 'CONVERSACIONES INTERDIMENSIONALES Período IV Edición 00';
        $new = 'Sin curiosidad, el ser humano sería una máquina repetitiva, recurrente.';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('CONVERSACIONES', strip_tags($html));
        $this->assertStringContainsString('curiosidad', strip_tags($html));
        $this->assertStringContainsString('<del', $html);
        $this->assertStringContainsString('<ins', $html);
    }

    /** @test */
    public function it_does_not_cut_words_when_formatting_differs()
    {
        $old = '{style=text-align: center*}será el momento';
        $new = '{style=text-align: center}_será el momento';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('será', $html);
        $this->assertStringContainsString('el momento', $html);
        $this->assertStringNotContainsString('center</del>}<ins>_</ins>', $html);
    }

    /** @test */
    public function it_handles_footnote_anchor_removal_cleanly()
    {
        $old = 'Hermes Trismegisto<a name="footnote-2"></a>[<sup>2</sup>](#note-2).';
        $new = 'Hermes Trismegisto[<sup>2</sup>](#note-2).';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('Hermes Trismegisto', strip_tags($html));
        $this->assertStringContainsString('#note-2', strip_tags($html));
    }

    /** @test */
    public function it_handles_css_style_and_html_markup_change()
    {
        $old = '{style=text-align:center}**<u>77. LA PREGUNTA CORRECTA</u>**';
        $new = '{style=text-align: center;}**77\. LA PREGUNTA CORRECTA**';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('LA PREGUNTA CORRECTA', strip_tags($html));
        $this->assertStringContainsString('77', strip_tags($html));
    }

    /** @test */
    public function it_handles_footnote_removal_with_adjacent_word()
    {
        $old = "Some text.\nNIJA.<a name=\"footnote-3\"></a>[<sup>3</sup>](#note-3)\nMore text.";
        $new = "Some text.\nNIJA.[<sup>3</sup>](#note-3)\nMore text.";

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('NIJA', strip_tags($html));
        $this->assertStringContainsString('#note-3', strip_tags($html));
    }

    /** @test */
    public function it_handles_footnote_removal_single_line()
    {
        $old = 'NIJA.<a name="footnote-3"></a>[<sup>3</sup>](#note-3) rest of paragraph.';
        $new = 'NIJA.[<sup>3</sup>](#note-3) rest of paragraph.';

        $html = DiffRenderer::render($old, $new);

        $this->assertStringContainsString('NIJA', strip_tags($html));
        $this->assertStringContainsString('#note-3', strip_tags($html));
        $this->assertStringContainsString('rest of paragraph', strip_tags($html));
    }
}
