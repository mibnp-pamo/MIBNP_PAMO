<?php

namespace Tests\Unit;

use App\Support\ScientificNameFormatter;
use PHPUnit\Framework\TestCase;

class ScientificNameFormatterTest extends TestCase
{
    public function test_it_italicizes_scientific_name_titles(): void
    {
        $this->assertSame(
            '<em>Bubalus mindorensis</em>',
            ScientificNameFormatter::formatName('Bubalus mindorensis')
        );
    }

    public function test_it_leaves_common_names_as_plain_text(): void
    {
        $this->assertSame(
            'Mindoro Hornbill',
            ScientificNameFormatter::formatName('Mindoro Hornbill')
        );
    }

    public function test_it_italicizes_scientific_names_inside_body_copy(): void
    {
        $this->assertSame(
            '<em>Ficus nota</em> supports native fruiting habitats.',
            ScientificNameFormatter::formatText(
                'Ficus nota supports native fruiting habitats.',
                ['Ficus nota']
            )
        );
    }
}
