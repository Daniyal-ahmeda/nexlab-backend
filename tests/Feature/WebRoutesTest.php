<?php

test('welcome page returns successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('admin web routes return successful response', function () {
    $dashboardResponse = $this->get('/admin');
    $dashboardResponse->assertStatus(200);

    $loginResponse = $this->get('/admin/login');
    $loginResponse->assertStatus(200);
});
