<?php
/**
 * The template for displaying the search form.
 *
 * @package Go
 */

?>
<div
id="js-site-search"
class="site-search"
itemscope
itemtype="http://schema.org/WebSite"
<?php if ( Go\AMP\is_amp() ) { ?>
	on="tap:AMP.setState( { searchModalActive: true } )"
<?php } ?>
>

	<?php if ( defined('WAFF_PARTIALS') && 'diag' === WAFF_PARTIALS ) : /* DIAG */ ?>

		<form role="search" id="searchform" class="search-form is-formatted mt-0" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="form-group g-0 w-100 --bg-action-2">
					<div class="input-group p-2">
					<meta itemprop="target" content="<?php echo esc_url( home_url( '/' ) ); ?>/?s={s}" />
					<label for="search-field visually-hidden">
						<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'go' ); ?></span>
					</label>
					<input class="form-control form-control-lg focus-action-1 input input--search search-form__input p-2 ms-1 me-2 flex-fill w-50" itemprop="query-input" type="search" id="search-field" autocomplete="off" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'go' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
					<button class="btn btn-action-2 btn-lg search-input__button d-flex flex-center rounded-sm rounded" type="submit">
						<span class="screen-reader-text search-input__label"><?php echo esc_html_x( 'Submit', 'submit button', 'go' ); ?></span>
						<i class="fas fa-search text-light"></i>
					</button>
				</div>
			</div>
		</form>

	<?php else : /* OTHERS */ ?>

		<form role="search" id="searchform" class="search-form is-formatted mt-0" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="form-group g-0">
					<div class="input-group mb-3">
					<meta itemprop="target" content="<?php echo esc_url( home_url( '/' ) ); ?>/?s={s}" />
					<label for="search-field visually-hidden">
						<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'go' ); ?></span>
					</label>
					<input class="form-control form-control-lg focus-dark input input--search search-form__input" itemprop="query-input" type="search" id="search-field" autocomplete="off" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'go' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
					<button class="btn btn-outline-dark btn-light btn-lg focus-dark rounded-0 form-control form-control-lg search-input__button" type="submit">
						<span class="screen-reader-text search-input__label"><?php echo esc_html_x( 'Submit', 'submit button', 'go' ); ?></span>
						OK <i class="icon icon-ok"></i>
					</button>
				</div>
			</div>
		</form>

	<?php endif; ?>

</div>
