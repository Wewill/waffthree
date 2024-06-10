<?php
/**
 * Displays the loginout modal
 *
 * @package Go
 */
?>

<div class="modal fade p-4 py-md-5" tabindex="-1" role="dialog" id="modalLoginout">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header p-5 pb-4 border-bottom-0">
	 	<div class="logo"><img class="img-responsive h-50-px" src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_dark_url' ); ?>" alt="<?= get_bloginfo('name'); ?> : <?= get_bloginfo('description'); ?>"></div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-5 pt-0">

		<?php if ( !is_user_logged_in() ) : ?>

		<h4 class="fs-6 fw-bold mb-3"><?= esc_html_x( 'Log-in', 'Loginout modal', 'waff' ); ?></h4>
        <form action="<?= esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
          <div class="form-floating mb-3">
            <input type="text" class="form-control rounded-3" id="user_login" name="user_login" placeholder="name@example.com">
            <label for="user_login"><?= esc_html_x( 'User or email address', 'Loginout modal', 'waff' ); ?></label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control rounded-3" id="user_password" name="user_password" placeholder="Password">
            <label for="user_password"><?= esc_html_x( 'Password', 'Loginout modal', 'waff' ); ?></label>
          </div>
		  <input type="hidden" name="redirect_to" value="<?= esc_url( get_permalink() ); ?>">
		  <input type="hidden" name="action" value="custom_login">		  
          <button class="w-100 mb-2 btn btn-lg rounded-3 btn-action-2 d-flex flex-center" type="submit" name="submit">
		  <i class="bi bi-box-arrow-in-right fs-5 lh-0 me-2"></i>
		  	<?= esc_html_x( 'Sign-in', 'Loginout modal', 'waff' ); ?>
		  </button>
          <small class="text-body-secondary">By clicking Sign up, you agree to the terms of use.</small>
          
		  <hr class="my-4 op-1">

          <h4 class="fs-6 fw-bold mb-3 text-action-1"><?= esc_html_x( 'Or register for free if you are new here...', 'Loginout modal', 'waff' ); ?></h4>
          <a href="<?= esc_url( wp_registration_url() ); ?>" class="w-100 py-2 mb-2 btn btn-outline-action-1 rounded-3 d-flex flex-center">
		  <i class="bi bi-person-add fs-5 lh-0 me-2"></i>
            <?= esc_html_x( 'Sign-up', 'Loginout modal', 'waff' ); ?>
			</a>
        </form>

		<?php else : 
			
			$current_user = wp_get_current_user();
			$user_email = $current_user->user_email;
			
			// Retrieve user meta data (example: first name and last name)
			$first_name = get_user_meta($current_user->ID, 'first_name', true);
			$last_name = get_user_meta($current_user->ID, 'last_name', true);
			$user_geography = get_user_meta($current_user->ID, 'user_geography');
			$user_structure = get_user_meta($current_user->ID, 'user_structure');
			?>

			<h6 class="fw-bold mb-3 text-action-3 mb-3"><?= esc_html_x( 'Profile', 'Loginout modal', 'waff' ); ?></h6>
			<h4 class="fs-4 fw-bold mb-2"><?= esc_html($current_user->display_name); ?></h4>
			<p class="mb-1 op-5"><?= esc_html($current_user->user_email); ?></p> 

			<?php if ( $user_geography ) : ?>
			<p class="mb-1">Geography : <?php foreach($user_geography as $geography) { $term = get_term_by('slug', $geography, 'geography'); if (!is_wp_error($term)) { echo $term->name; } } ?></p> 
			<?php endif; ?>

			<?php if ( $user_structure ) : ?>
			<p class="mb-1">Structure : <?php foreach($user_structure as $structure) { echo get_the_title($structure); } ?></p> 
			<?php endif; ?>

			<hr class="my-4 op-1">

			<a href="<?= esc_url( wp_logout_url( home_url() ) ); ?>" class="w-100 py-2 mb-2 btn btn-outline-action-2 rounded-3 d-flex flex-center">
			<i class="bi bi-person-add fs-5 lh-0 me-2"></i>
			<?= esc_html_x( 'Sign-out', 'Loginout modal', 'waff' ); ?>
			</a>

		<?php endif; ?>

      </div>
    </div>
  </div>
</div><!-- .loginout-modal -->

<?php 

