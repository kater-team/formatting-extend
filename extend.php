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
                        // $configurator->MediaEmbed->add(
                        //     'youtube2',
                        //     [
                        //         'host'    => 'www.youtube.com',
                        //         'extract' => [
                        //             '!youtube\\.com/embed/(?\'id\'[-\\w]+)!',
                        //         ],
                        //         'iframe'  => [
                        //             'width'  => 760,
                        //             'height' => 450,
                        //             'src'    => 'https://www.youtube.com/embed/{@id}'
                        //         ]
                        //     ]
                        // );
                        $configurator->MediaEmbed->add(
                            "youtube2",
                            [
                                'amp' => [
                                    'custom-element' => 'amp-youtube',
                                    'src' => 'https://cdn.ampproject.org/v0/amp-youtube-0.1.js',
                                    'template' => '<amp-youtube layout="responsive" width="640" height="360" data-param-list="{@list}" data-param-start="{@t}" data-videoid="{@id}"/>'
                                ],
                                'attributes' => [
                                    'id' => ['filterChain' => ['#identifier'], 'required' => false],
                                    't' => ['filterChain' => ['#timestamp']]
                                ],
                                'example' => [
                                    'https://www.youtube.com/watch?v=-cEzsCAzTak',
                                    'https://youtu.be/-cEzsCAzTak',
                                    'https://www.youtube.com/watch?feature=player_detailpage&v=jofNR_WkoCE#t=40',
                                    'https://www.youtube.com/watch?v=pC35x6iIPmo&list=PLOU2XLYxmsIIxJrlMIY5vYXAFcO5g83gA'
                                ],
                                'extract' => [
                                    '!youtube\\.com/embed/(?\'id\'[-\\w]+)!',
                                    '!youtube\\.com/(?:watch.*?v=|shorts/|v/|attribution_link.*?v%3D)(?\'id\'[-\\w]+)!',
                                    '!youtu\\.be/(?\'id\'[-\\w]+)!', '@[#&?]t=(?\'t\'\\d[\\dhms]*)@',
                                    '![&?]list=(?\'list\'[-\\w]+)!'
                                ],
                                'homepage' => 'https://www.youtube.com/',
                                'host' => ['youtube.com', 'youtu.be'],
                                'iframe' => [
                                    'src' => 'https://www.youtube.com/embed/<xsl:value-of select="@id"/><xsl:if test="@list">?list=<xsl:value-of select="@list"/></xsl:if><xsl:if test="@t"><xsl:choose><xsl:when test="@list">&amp;</xsl:when><xsl:otherwise>?</xsl:otherwise></xsl:choose>start=<xsl:value-of select="@t"/></xsl:if>',
                                    'style' => ['background' => 'url(https://i.ytimg.com/vi/{@id}/hqdefault.jpg) 50% 50% / cover']
                                ],
                                'name' => 'YouTube',
                                'oembed' => [
                                    'endpoint' => 'https://www.youtube.com/oembed',
                                    'scheme' => 'https://www.youtube.com/watch?v={@id}'
                                ],
                                'scrape' => [],
                                'source' => 'https://support.google.com/youtube/bin/answer.py?hl=en&answer=171780',
                                'tags' => ['livestreaming', 'videos']
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
