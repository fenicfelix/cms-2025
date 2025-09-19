<?php

use App\Models\Video;

function prepare_post_body($post, $ad_one = "", $ad_two = "")
    {
        $body = $post->body;
        $related_posts = fetch_related_posts($post, 3);

        $ad_code_one = "";
        $ad_code_two = "";

        if (get_option($ad_one)) {
            $ad_code_one = '
            <div class="col-12 col-md-6 offset-md-3 text-center ad py-4 mb-4">
                ' . get_option($ad_one) . '
            </div>';
        }

        if (get_option($ad_two)) {
            $ad_code_two = '
            <div class="col-12 col-md-6 offset-md-3 text-center ad py-4 mb-4">
                ' . get_option($ad_two) . '
            </div>';
        }


        $closing_p = '</p>';
        $paragraphs = explode($closing_p, $body);

        foreach ($paragraphs as $index => $paragraph) {

            if (trim($paragraph)) {
                $paragraphs[$index] .= $closing_p;
            }

            if ($index == 2) {
                $paragraphs[$index] .= $ad_code_one;
            }

            if ($index == 6) {
                $paragraphs[$index] .= $ad_code_two;
            }

            if ($index == 4) {
                if (sizeof($related_posts) > 0) {
                    $also_read = view('frontend.templates.also-read', ['related_posts' => $related_posts]);
                    $paragraphs[$index] .= $also_read;
                }
            }
        }


        return implode('', $paragraphs);;
    }

    function social_icons()
    {
        return load_theme('templates.social-icons');
    }

    function fetch_youtube_videos($limit, $size)
    {
        $videos = Video::isPublished()->orderBy('published_at', 'DESC')->skip(0)->take(10)->get();
        return view('theme.tv47.templates.youtube-videos', ['videos' => $videos, 'size' => 'sm']);
    }
