<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for rudraspirit_mukhi_number() — the helper that maps a product to a
 * Mukhi number (1..14) by parsing its name, then its tags. Pure logic, no DB.
 */
class MukhiNumberTest extends TestCase
{
    private function product(string $name, string $tags = '')
    {
        // Plain object: no getTranslation(), so the helper falls back to ->name.
        return (object) ['name' => $name, 'tags' => $tags];
    }

    public function test_parses_number_from_name(): void
    {
        $this->assertSame(5, rudraspirit_mukhi_number($this->product('5 Mukhi Rudraksha')));
        $this->assertSame(14, rudraspirit_mukhi_number($this->product('14 Mukhi Rudraksha (Devamani)')));
        $this->assertSame(1, rudraspirit_mukhi_number($this->product('1 mukhi rudraksha')));
    }

    public function test_falls_back_to_tags(): void
    {
        $this->assertSame(8, rudraspirit_mukhi_number($this->product('Ganesha Bead', 'rare, 8 Mukhi, ganesha')));
    }

    public function test_returns_null_when_no_mukhi(): void
    {
        $this->assertNull(rudraspirit_mukhi_number($this->product('Rudraksha Mala')));
        $this->assertNull(rudraspirit_mukhi_number(null));
    }
}
