<?php
/*
Plugin Name: Linkpost Pro
Description: A plugin to create posts with links.
Version: 1.0
Author: Blaze-Techz
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue Custom Styles
add_action('admin_enqueue_scripts', 'clp_enqueue_admin_styles');

function clp_enqueue_admin_styles($hook) {
    if ($hook != 'toplevel_page_clp-link-poster') {
        return;
    }
    wp_enqueue_style('clp-admin-styles', plugins_url('admin-style.css', __FILE__));
}

// Admin Menu Setup
add_action('admin_menu', 'clp_admin_menu');

function clp_admin_menu() {
    add_menu_page(
        'Link Poster',
        'Link Poster',
        'manage_options',
        'clp-link-poster',
        'clp_link_poster_page',
        'dashicons-admin-links',
        6
    );
}

// Admin Page Content
// Admin Page Content
function clp_link_poster_page() {
    ?>
    <div class="clp-wrap">
        <h1 class="clp-title">Create Post with Links</h1>
        <form method="post" action="" class="clp-form" id="clp-link-form">
            <div id="clp-links-wrapper">
                <div class="clp-link-group">
                    <input type="text" name="clp_link_name[]" placeholder="Link Name" class="clp-input" required>
                    <input type="url" name="clp_link_url[]" placeholder="Link URL" class="clp-input" required>
                </div>
            </div>
            <button type="button" id="add-more-links" class="button">Add More Links</button><br><br>
            <input type="text" name="clp_post_title" placeholder="Enter Post Title" class="clp-input" required><br><br>
            <input type="submit" name="clp_submit" class="button button-primary clp-button" value="Create Post">
        </form>
    </div>

    <script type="text/javascript">
        document.getElementById('add-more-links').addEventListener('click', function() {
            var wrapper = document.getElementById('clp-links-wrapper');
            var newLinkGroup = document.createElement('div');
            newLinkGroup.classList.add('clp-link-group');
            newLinkGroup.innerHTML = `
                <input type="text" name="clp_link_name[]" placeholder="Link Name" class="clp-input" required>
                <input type="url" name="clp_link_url[]" placeholder="Link URL" class="clp-input" required>
            `;
            wrapper.appendChild(newLinkGroup);
        });
    </script>
    <?php

    if (isset($_POST['clp_submit'])) {
        clp_create_post_with_links($_POST['clp_link_name'], $_POST['clp_link_url'], $_POST['clp_post_title']);
    }
}

function clp_create_post_with_links($link_names, $link_urls, $title) {
    $content = '';
    $counter = 1;

    for ($i = 0; $i < count($link_names); $i++) {
        $name = sanitize_text_field($link_names[$i]);
        $url = esc_url($link_urls[$i]);

        // Centering and bold formatting for both title and link
        $content .= '<div style="text-align: center;">';
        $content .= '<h2><strong>EPISODE ' . $counter . '</strong></h2>';
        $content .= '<a href="' . $url . '" class="download-button" target="_blank"><strong>' . esc_html($name) . '</strong></a>';
        $content .= '</div><hr>';
        $counter++;
    }

    $post_data = array(
        'post_title'    => wp_strip_all_tags($title),
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_author'   => get_current_user_id(),
        'post_type'     => 'post',
    );

    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        echo '<div class="notice notice-success is-dismissible"><p>Post created successfully!</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>There was an error creating the post.</p></div>';
    }
}
