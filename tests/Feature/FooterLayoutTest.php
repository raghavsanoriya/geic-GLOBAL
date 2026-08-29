<?php

namespace Tests\Feature;

use Tests\TestCase;

class FooterLayoutTest extends TestCase
{
    public function test_footer_bottom_surface_does_not_add_a_blank_top_band(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('padding-top: 0;', false)
            ->assertDontSee('padding-top: 42px;', false);
    }
}
