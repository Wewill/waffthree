<?php
// 1. Add a sortable column to the partner post type admin list
// Use for debug purposes
// add_filter('manage_partner_posts_columns', function($columns) {
//     $columns = array_slice($columns, 0, 1, true) +
//         ['order' => __('Order', 'waff')] +
//         array_slice($columns, 1, null, true);
//     return $columns;
// });

// add_action('manage_partner_posts_custom_column', function($column, $post_id) {
//     if ($column === 'order') {
//         echo (int) get_post_field('menu_order', $post_id);
//     }
// }, 10, 2);

/**
 * Registers drag-and-drop manual ordering (menu_order) on the admin list
 * table for a given post type.
 *
 * Everything (JS enqueue, AJAX handler, default admin ordering) is scoped to
 * $post_type, and the AJAX action / nonce name is derived from it, so several
 * post types can be registered side by side - and, if a third-party plugin
 * (e.g. "wa-partners") registers its own 'partner_menuorder' AJAX handler,
 * enabling this here for another post type (e.g. 'hs-slider') will never
 * collide with it.
 *
 * @param string $post_type  Post type slug to enable drag-and-drop ordering for.
 * @param string $capability Capability required to reorder. Default 'edit_others_posts'.
 */
if ( ! function_exists( 'waff_register_menuorder_ui' ) ) {
    function waff_register_menuorder_ui($post_type, $capability = 'edit_others_posts') {
        $ajax_action = str_replace('-', '_', $post_type) . '_menuorder';

        // Enqueue vanilla JS for drag-and-drop on edit.php for this post type
        add_action('admin_enqueue_scripts', function($hook) use ($post_type, $ajax_action) {
            if ($hook !== 'edit.php' || get_post_type() !== $post_type) return;
            ?>
            <script>
            // Vanilla JS drag-and-drop for <?php echo esc_js($post_type); ?> ordering
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.querySelector('.wp-list-table');
                if (!table) return;

                let draggingRow = null;
                let placeholder = document.createElement('tr');
                placeholder.className = 'menuorder-placeholder';
                placeholder.innerHTML = '<td colspan="' + table.rows[0].cells.length + '" style="background:#f9f9f9; border:2px dashed #ccc; height:40px;"></td>';

                table.querySelectorAll('tbody > tr').forEach(row => {
                    row.draggable = true;
                    row.addEventListener('dragstart', function(e) {
                        draggingRow = row;
                        row.style.opacity = '0.5';
                    });
                    row.addEventListener('dragend', function() {
                        // Move row to placeholder position if placeholder exists
                        let ph = table.querySelector('.menuorder-placeholder');
                        if (ph && draggingRow) {
                            ph.parentNode.insertBefore(draggingRow, ph);
                            ph.remove();
                            updateOrder();
                        } else if (ph) {
                            ph.remove();
                        }
                        draggingRow = null;
                        row.style.opacity = '';
                    });
                    row.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        if (!draggingRow || row === draggingRow) return;
                        let ph = table.querySelector('.menuorder-placeholder');
                        if (ph) ph.remove();
                        if (e.clientY < row.getBoundingClientRect().top + row.offsetHeight / 2) {
                            row.parentNode.insertBefore(placeholder, row);
                        } else {
                            row.parentNode.insertBefore(placeholder, row.nextSibling);
                        }
                    });
                });

                function updateOrder() {
                    // Collect post IDs in new order
                    let ids = [];
                    table.querySelectorAll('tbody > tr').forEach(row => {
                        if (row.id && row.id.startsWith('post-')) {
                            ids.push(row.id.replace('post-', ''));
                        }
                    });
                    if (ids.length === 0) return;
                    // Send AJAX request
                    fetch(ajaxurl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'action=<?php echo esc_js($ajax_action); ?>&nonce=' + encodeURIComponent('<?php echo wp_create_nonce($ajax_action); ?>') + '&ids=' + encodeURIComponent(JSON.stringify(ids))
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            // Show a temporary notice above the table
                            let notice = document.createElement('div');
                            notice.className = 'notice notice-success is-dismissible';
                            notice.style.marginTop = '10px';
                            notice.innerHTML = '<p><?php echo esc_js(__('Order successfully updated!', 'waff')); ?></p>';
                            let tableWrap = document.querySelector('.wrap h1') || document.querySelector('.wrap');
                            if (tableWrap && tableWrap.parentNode) {
                                tableWrap.parentNode.insertBefore(notice, tableWrap.nextSibling);
                                setTimeout(() => {
                                    notice.remove();
                                }, 2000);
                            }
                        }
                    });
                }
            });
            </script>
            <style>
            .menuorder-placeholder td { background: #f9f9f9 !important; border: 2px dashed #ccc !important; }
            tr[draggable="true"] { cursor: move; }
            </style>
            <?php
        });

        // AJAX handler to save order
        add_action('wp_ajax_' . $ajax_action, function() use ($ajax_action, $capability) {
            if (!current_user_can($capability) || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $ajax_action)) {
                wp_send_json_error('Permission denied');
            }
            $ids = json_decode(stripslashes($_POST['ids'] ?? '[]'));
            if (!is_array($ids)) wp_send_json_error('Invalid data');
            foreach ($ids as $i => $post_id) {
                wp_update_post([
                    'ID' => (int)$post_id,
                    'menu_order' => $i
                ]);
            }
            wp_send_json_success();
        });

        // Order posts by menu_order by default in admin
        add_action('pre_get_posts', function($query) use ($post_type) {
            if (is_admin() && $query->is_main_query() && $query->get('post_type') === $post_type && !$query->get('orderby')) {
                $query->set('orderby', 'menu_order');
                $query->set('order', 'ASC');
            }
        });
    }
}

// Enable drag-and-drop manual ordering for 'partner' posts.
// NOTE: the "wa-partners" plugin implements this same feature natively for
// 'partner'. If that plugin is active on this install, remove/comment this
// call below to avoid registering duplicate drag handlers on the same screen
// (its own AJAX action/nonce is already namespaced to 'partner_menuorder',
// same as here, so keep only one of the two active at a time).
waff_register_menuorder_ui('partner');

// Enable drag-and-drop manual ordering for 'hs-slider' posts.
// Uses its own AJAX action/nonce ('hs_slider_menuorder'), so it runs
// independently and can cohabit safely with the "wa-partners" plugin above.
//waff_register_menuorder_ui('hs-slider');
