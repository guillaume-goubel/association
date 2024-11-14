<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'base' => [
        'path' => './assets/js/base/base.js',
        'entrypoint' => true,
    ],
    'mapModale' => [
        'path' => './assets/js/base/map-modal.js',
        'entrypoint' => true,
    ],
    'blog' => [
        'path' => './assets/js/pages/blog.js',
        'entrypoint' => true,
    ],
    'agenda' => [
        'path' => './assets/js/pages/agenda.js',
        'entrypoint' => true,
    ],
    'calendar' => [
        'path' => './assets/js/pages/calendar.js',
        'entrypoint' => true,
    ],
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'home' => [
        'path' => './assets/js/pages/home.js',
        'entrypoint' => true,
    ],
    'admin-index' => [
        'path' => './assets/js/pages/admin/index.js',
        'entrypoint' => true,
    ],
    'admin-activity' => [
        'path' => './assets/js/pages/admin/activity.js',
        'entrypoint' => true,
    ],
    'admin-event' => [
        'path' => './assets/js/pages/admin/event.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
];
