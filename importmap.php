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
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'register' => [
        'path' => './assets/js/register.js',
        'entrypoint' => true,
    ],
    'login' => [
        'path' => './assets/js/login.js',
        'entrypoint' => true,
    ],
    'addPhoto' => [
        'path' => './assets/js/addPhoto.js',
        'entrypoint' => true,
    ],
    'getGeolocation' => [
        'path' => './assets/js/getGeolocation.js',
        'entrypoint' => true,
    ],
    'showMap' => [
        'path' => './assets/js/showMap.js',
        'entrypoint' => true,
    ],
    'listMap' => [
        'path' => './assets/js/listMap.js',
        'entrypoint' => true,
    ],
    'calculateTime' => [
        'path' => './assets/js/calculateTime.js',
        'entrypoint' => true,
    ],
    'detailsCarpool' => [
        'path' => './assets/js/detailsCarpool.js',
        'entrypoint' => true,
    ],
    'dateSearch' => [
        'path' => './assets/js/dateSearch.js',
        'entrypoint' => true,
    ],
    'reviewStar' => [
        'path' => './assets/js/reviewStar.js',
        'entrypoint' => true,
    ],
    'reviewWebStar' => [
        'path' => './assets/js/reviewWebStar.js',
        'entrypoint' => true,
    ],
    'addStaff' => [
        'path' => './assets/js/addStaff.js',
        'entrypoint' => true,
    ],
    'carpoolsGraphSelect' => [
        'path' => './assets/js/carpoolsGraphSelect.js',
        'entrypoint' => true,
    ],
    'creditsGraphSelect' => [
        'path' => './assets/js/creditsGraphSelect.js',
        'entrypoint' => true,
    ],
    'carpoolsGraph' => [
        'path' => './assets/js/carpoolsGraph.js',
        'entrypoint' => true,
    ],
    'creditsGraph' => [
        'path' => './assets/js/creditsGraph.js',
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
