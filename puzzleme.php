<?php
/*
   Plugin Name: PuzzleMe for WordPress
   Version: 1.1.5
   Description: Embed PuzzleMe puzzles in your posts and pages with a shortcode
   Author: Amuse Labs
   Author URI: https://www.amuselabs.com/
   License: GPLv2 or later
   License URI: https://www.gnu.org/licenses/gpl-2.0.html
   */

function load_puzzle_scripts() {

    if ( ! defined( 'PuzzleMe_BasePath' ) ) {
        define( 'PuzzleMe_BasePath', 'https://amuselabs.com/pmm/' );
    }

    $footer_script_HTML = '
    
    <script nowprocket src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script nowprocket id="pm-script" src="' . PuzzleMe_BasePath . 'js/puzzleme-embed.js"></script>
    <script nowprocket>
    PM_Config.PM_BasePath = "' . PuzzleMe_BasePath . '";
    </script>

    ';
    echo($footer_script_HTML);
}


function puzzleme_iframe_generator($attributes)
{
    add_action('wp_footer', 'load_puzzle_scripts');

    $allowedHTMLtags = array(
        'div' => array(
            'class' => true,
            'data-id' => true,
            'data-set' => true,
            'data-puzzletype' => true,
            'data-height' => true,
            'data-embedparams' => true,
            'data-page' => true
        ),
        'script' => array(
            'id' => true,
            'src' => true,
            'nowprocket' => true
        )
    );

    $valid_embed_types = array('crossword', 'sudoku', 'wordsearch', 'quiz', 'krisskross', 'wordf', 'codeword', 'wordrow', 'jigsaw', 'date-picker');

    $embed_html = '

        <div class="pm-embed-div" $id data-set="$set" $embed_type data-height="700px" $embed_params></div>

        ';

    $embed_variables = array(
        '$embed_type' => '',
        '$set' => '',
        '$embed_params' => '',
        '$id' => ''
    );

    if (empty($attributes)) {
        return "Parameters are missing in the shortcode. Please copy the correct shortcode from your PuzzleMe dashboard.";
    } else {
        if (isset($attributes['set'])) {
            if (isset($attributes['type'])) {
                if (in_array($attributes['type'], $valid_embed_types)) {

                    $embed_variables['$embed_type'] = sanitize_text_field($attributes['type']);
                    $embed_variables['$set'] = sanitize_text_field($attributes['set']);
                    if (isset($attributes['embedparams'])) {
                        $embed_variables['$embed_params'] = 'data-embedparams="embed=wp&' . sanitize_text_field($attributes['embedparams']) . '"';
                    } else {
                         $embed_variables['$embed_params'] = 'data-embedparams="embed=wp"' ;
                    }
                    if ($attributes['type'] != 'date-picker') {
                        $embed_variables['$embed_type'] = 'data-puzzleType="' . sanitize_text_field($attributes['type']) . '"';
                        if (isset($attributes['id'])) {
                            $embed_variables['$id'] = 'data-id="' . sanitize_text_field($attributes['id']) . '"';
                        } else {
                            return "Puzzle ID is missing in the shortcode. Please use the correct shortcode from your PuzzleMe dashboard.";
                        }
                    } else {
                        $embed_variables['$embed_type'] = 'data-page="date-picker"';
                    }
                    return wp_kses(strtr($embed_html, $embed_variables), $allowedHTMLtags);

                } else {
                    return "Invalid puzzle type is present in the shortcode. Please use the correct shortcode from your PuzzleMe dashboard.";
                }

            } else {
                return "Puzzle type is missing in the shortcode. Please use the correct shortcode from your PuzzleMe dashboard.";
            }

        } else {
            return "Set is missing in the shortcode. Please use the correct shortcode from your PuzzleMe dashboard.";
        }
    }
}

add_shortcode('puzzleme', 'puzzleme_iframe_generator');

?>