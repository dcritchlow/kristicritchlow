<?php

function excerpt_read_more_link($output) {
global $post;
return $output . '<a href="'. get_permalink($post->ID) . '"> Read More...</a>';
}
add_filter('the_excerpt', 'excerpt_read_more_link');

function twentythirteen_entry_date( $echo = true ) {
    if ( has_post_format( array( 'chat', 'status' ) ) )
        $format_prefix = _x( '%1$s on %2$s', '1: post format name. 2: date', 'twentythirteen' );
    else
        $format_prefix = '%2$s';

    $date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
        esc_url( get_permalink() ),
        esc_attr( sprintf( __( '%s', 'twentythirteen' ), the_title_attribute( 'echo=0' ) ) ),
        esc_attr( get_the_date( 'c' ) ),
        esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
    );

    if ( $echo )
        echo $date;

    return $date;
}

/**
 * Outputs a complete commenting form for use within a template.
 * Most strings and form fields may be controlled through the $args array passed
 * into the function, while you may also choose to use the comment_form_default_fields
 * filter to modify the array of default fields if you'd just like to add a new
 * one or remove a single field. All fields are also individually passed through
 * a filter of the form comment_form_field_$name where $name is the key used
 * in the array of fields.
 *
 * @since 3.0.0
 * @param array $args Options for strings, fields etc in the form
 * @param mixed $post_id Post ID to generate the form for, uses the current post if null
 * @return void
 */
function comment_form_child( $args = array(), $post_id = null ) {
    if ( null === $post_id )
        $post_id = get_the_ID();
    else
        $id = $post_id;

    $commenter = wp_get_current_commenter();
    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';

    if ( ! isset( $args['format'] ) )
        $args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';

    $req      = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $html5    = 'html5' === $args['format'];
    $fields   =  array(
        'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
        'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
        'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
            '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
    );

    $required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );
    $defaults = array(
        'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
        'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
        'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
        'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
        'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
        'comment_notes_after'  => '',
        'id_form'              => 'commentform',
        'id_submit'            => 'submit',
        'title_reply'          => __( 'Leave a Reply' ),
        'title_reply_to'       => __( 'Leave a Reply to %s' ),
        'cancel_reply_link'    => __( 'Cancel reply' ),
        'label_submit'         => __( 'Post Comment' ),
        'format'               => 'xhtml',
    );

    $args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

    ?>
    <?php if ( comments_open( $post_id ) ) : ?>
        <?php do_action( 'comment_form_before' ); ?>
        <div id="respond" class="comment-respond">
            <h3 id="reply-title" class="comment-reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?> <small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small></h3>
            <?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
                <?php echo $args['must_log_in']; ?>
                <?php do_action( 'comment_form_must_log_in_after' ); ?>
            <?php else : ?>
                <form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>" class="comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
                    <?php do_action( 'comment_form_top' ); ?>
                    <?php if ( is_user_logged_in() ) : ?>
                        <?php echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity ); ?>
                        <?php do_action( 'comment_form_logged_in_after', $commenter, $user_identity ); ?>
                    <?php else : ?>
                        <?php echo $args['comment_notes_before']; ?>
                        <?php
                        do_action( 'comment_form_before_fields' );
                        foreach ( (array) $args['fields'] as $name => $field ) {
                            echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
                        }
                        do_action( 'comment_form_after_fields' );
                        ?>
                    <?php endif; ?>
                    <?php echo apply_filters( 'comment_form_field_comment', $args['comment_field'] ); ?>
                    <?php echo $args['comment_notes_after']; ?>
                    <p class="form-submit">
                        <input name="submit" type="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" />
                        <?php comment_id_fields( $post_id ); ?>
                    </p>
                    <?php do_action( 'comment_form', $post_id ); ?>
                </form>
            <?php endif; ?>
        </div><!-- #respond -->
        <?php do_action( 'comment_form_after' ); ?>
    <?php else : ?>
        <?php do_action( 'comment_form_comments_closed' ); ?>
    <?php endif; ?>
<?php
}

function add_iframe($initArray) {
    $initArray['extended_valid_elements'] = "iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]";
    return $initArray;
}
add_filter('tiny_mce_before_init', 'add_iframe');