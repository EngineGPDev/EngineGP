<?php

/*
 * Copyright 2018-2025 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$aStyle = [
    'default' => [
        'fon' => ['R' => 232, 'G' => 235, 'B' => 240],
        'border' => ['R' => 220, 'G' => 220, 'B' => 220],
        'graph' => ['R' => 255, 'G' => 255, 'B' => 255, 'Surrounding' => -200, 'Alpha' => 10],
        'line' => ['R' => 68, 'G' => 148, 'B' => 224],
        'leftbox' => ['R' => 0, 'G' => 0, 'B' => 0],
        'box' => ['R' => 0, 'G' => 0, 'B' => 0],
        'boxcolor' => ['R' => 255, 'G' => 255, 'B' => 255],
        'color' => ['R' => 0, 'G' => 0, 'B' => 0],
        'progress' => ['R' => 68, 'G' => 148, 'B' => 224],
    ],

    'black' => [
        'fon' => ['R' => 0, 'G' => 0, 'B' => 0],
        'border' => ['R' => 232, 'G' => 235, 'B' => 240],
        'graph' => ['R' => 232, 'G' => 235, 'B' => 240, 'Surrounding' => -200, 'Alpha' => 100],
        'line' => ['R' => 68, 'G' => 148, 'B' => 224],
        'leftbox' => ['R' => 255, 'G' => 255, 'B' => 255],
        'box' => ['R' => 255, 'G' => 255, 'B' => 255],
        'boxcolor' => ['R' => 0, 'G' => 0, 'B' => 0],
        'color' => ['R' => 255, 'G' => 255, 'B' => 255],
        'progress' => ['R' => 68, 'G' => 148, 'B' => 224],
    ],

    'camo' => [
        'fon' => ['R' => 55, 'G' => 62, 'B' => 40],
        'border' => ['R' => 62, 'G' => 68, 'B' => 51],
        'graph' => ['R' => 46, 'G' => 50, 'B' => 37, 'Surrounding' => -200, 'Alpha' => 10],
        'line' => ['R' => 166, 'G' => 186, 'B' => 149],
        'leftbox' => ['R' => 32, 'G' => 35, 'B' => 27],
        'box' => ['R' => 46, 'G' => 50, 'B' => 37],
        'boxcolor' => ['R' => 210, 'G' => 225, 'B' => 181],
        'color' => ['R' => 136, 'G' => 156, 'B' => 99],
        'progress' => ['R' => 136, 'G' => 156, 'B' => 99, 'BoxBorderR' => 46, 'BoxBorderG' => 50, 'BoxBorderB' => 37],
    ],
];
