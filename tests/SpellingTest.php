<?php

declare(strict_types=1);

namespace Chr15k\Spelling\Test;

use Chr15k\Spelling\Lang;
use Chr15k\Spelling\Spelling;
use PHPUnit\Framework\TestCase;

class SpellingTest extends TestCase
{
    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->spelling = new Spelling;
    }

    public function testSpellingCheck()
    {
        foreach (Lang::$commonEnUs as $word) {
            $this->assertTrue($this->spelling->check($word));
            $this->assertFalse($this->spelling->check($word . 'zzz'));
            $this->assertIsBool($this->spelling->check($word));
            $this->assertIsBool($this->spelling->check($word . 'zzz'));
        }
    }

    public function testSpellingSuggestion()
    {
        foreach (Lang::$commonEnUs as $word) {
            $this->assertIsArray($this->spelling->suggestions($word . 'zzz'));
            $this->assertNull($this->spelling->suggestions($word));
        }
    }

    public function testSpellingAutoSuggestion()
    {
        foreach (Lang::$commonEnUs as $word) {
            $this->assertNotSame($word, $this->spelling->autoSuggestion($word) . 'zzz');
            $this->assertIsString($this->spelling->autoSuggestion($word) . 'zzz');
            $this->assertSame($word, $this->spelling->autoSuggestion($word));
            $this->assertIsString($this->spelling->autoSuggestion($word));
            $this->assertSame($word . ',', $this->spelling->autoSuggestion($word . ','));
        }
    }

    public function testAutoCorrect()
    {
        $this->assertSame(
            'He is in his office.',
            $this->spelling->autoCorrection('He is in his ooffice.')
        );
        $this->assertSame(
            'He sat under a tree.',
            $this->spelling->autoCorrection('He sat underr a tree.')
        );
        $this->assertSame(
            'There is someone at the door.',
            $this->spelling->autoCorrection('Thereg is someone at the door.')
        );
        $this->assertSame(
            'The crocodiles snapped at the boat.',
            $this->spelling->autoCorrection('The crocodiiles snappedf at the boat.')
        );
        $this->assertSame(
            'Put the books on the table.',
            $this->spelling->autoCorrection('Put the books on the tsble.')
        );
        $this->assertSame(
            'There are many apples on the tree.',
            $this->spelling->autoCorrection('There are mnny apples on the tree.')
        );
        $this->assertSame(
            'A gang stood in front of me.',
            $this->spelling->autoCorrection('A gang stoood in front of me.')
        );
        $this->assertSame(
            'The castle was heavily bombed during the war.',
            $this->spelling->autoCorrection('The castle was fheavily bombed during the war.')
        );
    }
}
