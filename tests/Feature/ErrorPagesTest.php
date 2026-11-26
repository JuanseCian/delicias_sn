<?php

test('the custom not found page keeps the Delicias branding and home link', function () {
    $response = $this->get('/esta-ruta-no-existe');

    $response->assertNotFound()
        ->assertSee('404')
        ->assertSee('Alguien se llevó')
        ->assertSee('href="'.route('home').'"', false)
        ->assertSee('icono_delicias.png');
});

test('the not found page skips the christmas theme even when it is enabled', function () {
    config(['delicias.christmas_theme_enabled' => true]);

    $response = $this->get('/esta-ruta-no-existe');

    $response->assertNotFound()
        ->assertDontSee('theme-christmas')
        ->assertDontSee('christmas-atmosphere');
});

test('regular pages keep the christmas theme when it is enabled', function () {
    config(['delicias.christmas_theme_enabled' => true]);

    $this->get(route('faq'))
        ->assertOk()
        ->assertSee('theme-christmas');
});
