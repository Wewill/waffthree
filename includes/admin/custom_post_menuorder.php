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

// 2. Enqueue vanilla JS for drag-and-drop on edit.php for partner
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'edit.php' || get_post_type() !== 'partner') return;
    ?>
    <script>
    // Vanilla JS drag-and-drop for partner ordering
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.querySelector('.wp-list-table');
        if (!table) return;

        let draggingRow = null;
        let startY = 0;
        let placeholder = document.createElement('tr');
        placeholder.className = 'menuorder-placeholder';
        placeholder.innerHTML = '<td colspan="' + table.rows[0].cells.length + '" style="background:#f9f9f9; border:2px dashed #ccc; height:40px;"></td>';

        table.querySelectorAll('tbody > tr').forEach(row => {
            row.draggable = true;
            row.addEventListener('dragstart', function(e) {
                console.log('Row drag started');
                draggingRow = row;
                startY = e.clientY;
                row.style.opacity = '0.5';
            });
            row.addEventListener('dragend', function() {
                console.log('Row drag ended');
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
                console.log('Row dragged over');
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
            console.log('Updating order...');
            // Collect post IDs in new order
            let ids = [];
            table.querySelectorAll('tbody > tr').forEach(row => {
                if (row.id && row.id.startsWith('post-')) {
                    ids.push(row.id.replace('post-', ''));
                }
            });
                        console.log('Ids...', ids);
            if (ids.length === 0) return;
            // Send AJAX request
            fetch(ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=partner_menuorder&nonce=' + encodeURIComponent('<?php echo wp_create_nonce('partner_menuorder'); ?>') + '&ids=' + encodeURIComponent(JSON.stringify(ids))
            }).then(r => r.json()).then(data => {
                            console.log('then data...', data);

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

// 3. AJAX handler to save order
add_action('wp_ajax_partner_menuorder', function() {
    if (!current_user_can('edit_others_posts') || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'partner_menuorder')) {
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

// 4. Order partner posts by menu_order by default in admin
add_action('pre_get_posts', function($query) {
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'partner' && !$query->get('orderby')) {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    }
});