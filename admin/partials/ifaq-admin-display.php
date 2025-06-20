<?php

/**
 * Provides the admin area view for the plugin
 *
 * This file contains the markup for the admin-facing aspects of the plugin.
 *
 * @link       https://abfahad.me
 * @since      1.0.0
 *
 * @package    Ifaq
 * @subpackage Ifaq/admin/partials
 */

if (!isset($_POST['ifaq_display_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ifaq_display_nonce'])), 'ifaq_display_action')) {
    echo '<div class="notice notice-error is-dismissible"><p><strong>Error:</strong> Security check failed.</p></div>';
} else {
    global $wpdb;
    $ifaq_db = new Ifaq_DB($wpdb);
    $ifaq_settings = maybe_unserialize(get_option('ifaq_settings'));
    $page = isset($_GET['paged']) ? max(1, intval(wp_unslash($_GET['paged']))) : 1;
    $per_page = $ifaq_settings['faqsPerPage'] ?? 10;
    $faqs_data = $ifaq_db->get_all_ifaqs($page, $per_page);
    $faqs = $faqs_data['faqs'];
    $current = $faqs_data['current_page'];
    $total = $faqs_data['total_pages'];
}
?>

<div class="ifaq-container">
    <h2>All Saved FAQs</h2>
    <div class="ifaq-accordion">
        <?php if (!empty($faqs)) : ?>
            <?php wp_nonce_field('ifaq_display_action', 'ifaq_display_nonce'); ?>
            <?php foreach ($faqs as $faq) : ?>
                <div class="ifaq-accordion-item">
                    <div class="ifaq-question">
                        <?php echo esc_html($faq->question); ?>
                        <span class="ifaq-icon">&#9662;</span>
                    </div>
                    <div class="ifaq-answer">
                        <?php echo esc_html($faq->answer); ?>
                        <div class="ifaq-meta">
                            Status: <span class="ifaq-status active"><?php echo esc_html($faq->status); ?></span> |
                            Created: <?php echo esc_html($faq->created_at); ?>
                        </div>
                        <div class="ifaq-actions">
                            <a href="<?php echo esc_attr(admin_url('admin.php?page=ifaq_add_new&action=edit_faq&id=' . intval($faq->id))); ?>"
                               class="edit">Edit</a>
                            <a href="#" class="delete"
                               data-faq-id="<?php echo esc_attr(intval($faq->id)); ?>">Delete</a>
                        </div>
                    </div>
                </div>
                <div id="ifaq-message" style="display:none; margin-top:10px; position:relative;">
                    <span class="ifaq-close"
                          style="position:absolute; right:10px; top:8px; cursor:pointer;">&times;</span>
                    <span class="ifaq-message-text"></span>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No FAQs found.</p>
        <?php endif; ?>
    </div>

    <div id="ifaq-pagination">
        <?php if ($total > 1): ?>
            <ul>
                <?php for ($i = 1; $i <= $total; $i++): ?>
                    <li><a href="<?php echo esc_url(add_query_arg('paged', $i)); ?>"
                           class="<?php echo ($i === $current) ? 'active' : ''; ?>"><?php echo esc_html($i); ?>
                        </a></li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>