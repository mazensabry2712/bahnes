<?php

test('the root route redirects to the default English homepage', function () {
    $this->get('/')
        ->assertRedirect(route('localized.home', ['locale' => 'en']));

    $this->get(route('localized.home', ['locale' => 'en']))
        ->assertOk();
});
