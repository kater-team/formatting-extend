<?php

/*
 * This file is part of kater/formatting-extend.
 *
 * Copyright (c) 2022 HHY.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Kater\FormattingExtend;

use Flarum\Extend;

// 视频解析
use FoF\Formatting\Listeners\FormatterConfigurator;
use s9e\TextFormatter\Configurator;
use s9e\TextFormatter\Configurator\Bundles\MediaPack;

return [
    
    // 添加视频解析  composer require FoF\Formatting
    (new Extend\Formatter())
    ->configure(function (Configurator $configurator) {
        $settings = app('flarum.settings');

        foreach (FormatterConfigurator::PLUGINS as $plugin) {
            $enabled = $settings->get('fof-formatting.plugin.'.strtolower($plugin));

            if ($enabled) {
                if ($plugin == 'MediaEmbed') {
                    $configurator->MediaEmbed->add(
                        'bilibili',
                        [
                            'host'    => 'www.bilibili.com',
                            'extract' => [
                                "!https://www.bilibili.com/video/BV(?'id'[0-9A-Z_a-z]+)!",
                                "!https://www.bilibili.com/video/av(?'id'\\d+)/!",
                                "!https://www.bilibili.com/video/av(?'id'\\d+)\\?p=(?'pid'\\w+)!"
                            ],
                            'iframe'  => [
                                'width'  => 760,
                                'height' => 450,
                                'src'    => 'https://player.bilibili.com/player.html?bvid={@id}'
                            ]
                        ]
                    );
                    $configurator->MediaEmbed->add(
                        'niconico',
                        [
                            'host'    => 'www.nicovideo.jp',
                            'extract' => [
                                "!https://www.nicovideo.jp/watch/(?'id'[0-9A-Z_a-z]+)!"
                            ],
                            'iframe'  => [
                                'width'  => 760,
                                'height' => 450,
                                'src'    => 'https://embed.nicovideo.jp/watch/{@id}'
                            ]
                        ]
                    );
                    (new MediaPack())->configure($configurator);
                } else {
                    $configurator->$plugin;
                }
            }
        }
    }),
];
