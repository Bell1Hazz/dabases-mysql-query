<?php
protected $middlewareAliases = [
    // ... existing middleware
    'admin' => \App\Http\Middleware\IsAdmin::class,
];