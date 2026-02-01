<?php

return [
    // Guest routes
    ['method' => 'GET', 'path' => '/', 'action' => 'AuthController@loginForm'],
    ['method' => 'GET', 'path' => '/login', 'action' => 'AuthController@loginForm'],
    ['method' => 'POST', 'path' => '/login', 'action' => 'AuthController@login'],
    ['method' => 'GET', 'path' => '/register', 'action' => 'AuthController@registerForm'],
    ['method' => 'POST', 'path' => '/register', 'action' => 'AuthController@register'],
    
    // Protected routes (require authentication)
    ['method' => 'GET', 'path' => '/dashboard', 'action' => 'DashboardController@index', 'middleware' => 'auth'],
    ['method' => 'POST', 'path' => '/logout', 'action' => 'AuthController@logout', 'middleware' => 'auth'],
    
    // Room CRUD routes
    ['method' => 'GET', 'path' => '/rooms', 'action' => 'RoomController@index', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/create', 'action' => 'RoomController@create', 'middleware' => 'auth'],
    ['method' => 'POST', 'path' => '/rooms/store', 'action' => 'RoomController@store', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/{id}/edit', 'action' => 'RoomController@edit', 'middleware' => 'auth'],
    ['method' => 'POST', 'path' => '/rooms/{id}/update', 'action' => 'RoomController@update', 'middleware' => 'auth'],
    ['method' => 'POST', 'path' => '/rooms/{id}/delete', 'action' => 'RoomController@delete', 'middleware' => 'auth'],
    
    // Quick filter routes
    ['method' => 'GET', 'path' => '/rooms/available', 'action' => 'RoomController@available', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/booked', 'action' => 'RoomController@booked', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/maintenance', 'action' => 'RoomController@maintenance', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/single', 'action' => 'RoomController@single', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/double', 'action' => 'RoomController@double', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/triple', 'action' => 'RoomController@triple', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/dorm', 'action' => 'RoomController@dorm', 'middleware' => 'auth'],
    
    // Search and API routes
    ['method' => 'GET', 'path' => '/rooms/search', 'action' => 'RoomController@search', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/suggest', 'action' => 'RoomController@suggest', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/stats', 'action' => 'RoomController@stats', 'middleware' => 'auth'],
    ['method' => 'GET', 'path' => '/rooms/export', 'action' => 'RoomController@export', 'middleware' => 'auth'],
];