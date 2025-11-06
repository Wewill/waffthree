<?php

//var_dump( $args );  // Everything
$page_atts = $args;

// Debut Relocate 
global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;

// Do not show quicklinks if no current_edition is set or films are not online
if( empty($current_edition_id) || $current_edition_films_are_online == false ){
	return;
}

// Get ticketing URL for current edition
$edition_ticketing_url 	= get_term_meta( $current_edition_id, 'wpcf-e-general-ticketing-url', true );

// Get parent section for current edition
$sections_args = array(
	'taxonomy'   => 'section',
	'parent'        => 0,
	'number'        => 1,				
	'hide_empty' => false,
	'meta_query' => array(
		array(
			'key'       => 'wpcf-select-edition',
			'value'     => $current_edition_id,
			'compare'   => '='
		)
	)
);
$top_parent_terms = get_terms($sections_args);
$top_parent_term_id = null;
$top_parent_term_permalink = null;
foreach( $top_parent_terms as $term ) {
	$selectedition 	= get_term_meta( $term->term_id, 'wpcf-select-edition', true ); // #41
	if( $selectedition == $current_edition_id && $term->parent == 0 ){
		$top_parent_term_id = $term->term_id;
		$top_parent_term_permalink = get_term_link( $term->term_id, 'section' );
		break;
	}
}

// If current post is a film, do check if film as a ticket url (wpcf-f-ticketing-url) and then use this link instead of edition ticketing url
$film_ticketing_url = null;
if( is_singular('film') ){
	$film_ticketing_url = get_post_meta( get_the_ID(), 'wpcf-f-ticketing-url', true );
	// echo '##DEBUG FILM TICKETING URL: '.$film_ticketing_url;
}

// Navigation links = title + URL + icon (optional) + description (optional) + bg color class (optional) + color class (optional)
// Billeterie, Programmation
$quicklinks = array(
	array(
		'title'       => !empty( $film_ticketing_url ) && $film_ticketing_url != '' ?'Réserver ma place':'Billetterie',
		'url'         => !empty( $film_ticketing_url ) && $film_ticketing_url != '' ?$film_ticketing_url:$edition_ticketing_url,
		'icon'        => 'bi bi-ticket',
		'description' => 'Réservez vos places en ligne afin de ne rien manquer aux projections et événements spéciaux.',
		'bg_color' 	  => 'action-2',
		'link_class' => 'text-color-heading link-color-heading --text-hover-color-light',
	),
	array(
		'title'       => 'Programmation',
		'url'         => $top_parent_term_id?$top_parent_term_permalink:'/programmation/',
		'icon'        => 'bi bi-collection-play', //-fill
		'description' => 'Découvrez la programmation complète : projections, films, compétition, masterclass, rencontres, invités, débats, rendez-vous étudiants, hors-les-murs et événements spéciaux du festival.',
		'bg_color' 	  => 'color-light',
		'link_class' => 'text-color-heading link-color-heading text-hover-action-1',
	),
);

/* Add this inline style for button : 
  border: 2px solid transparent;
  outline: 2px solid #3498db;   
  outline-offset: 2px;

  ====
  style="border: 2px solid transparent; outline: 2px solid var(--waff-<?php echo esc_attr( $link['bg_color'] ); ?>); outline-offset: 2px;"
*/

?>

<section id="quicklink" class="mt-0 mb-0 --contrast--light position-fixed bottom-0 start-0 zi-100">
	<ul class="quicklink-list list-unstyled d-flex justify-content-center align-items-stretch --gap-2 p-0 p-sm-4 m-0">
		<?php foreach ( $quicklinks as $link ) : ?>
		<li class="quicklink-item">
			<a 	href="<?php echo esc_url( $link['url'] ); ?>" 
				class="link d-flex flex-row justify-content-center align-items-center gap-2 text-center <?php echo esc_attr( $link['link_class'] ); ?> p-2 px-2 p-md-3 px-md-4 --rounded-full shadow-lg bg-<?php echo esc_attr( $link['bg_color'] ); ?>" 
				style="min-height: 33px;"
				data-bs-toggle="tooltip" data-toggle="tooltip" title="<?= $link['description']; ?>">
				<?php if ( ! empty( $link['icon'] ) ) : ?>
					<i class="<?php echo esc_attr( $link['icon'] ); ?> h5 mb-0" aria-hidden="true"></i>
				<?php endif; ?>
				<span class="quicklink-title h5 mb-0 <?= $link['title'] == 'Programmation'?'headline border-0 p-0 m-0':''; ?>"><?php echo esc_html( $link['title'] ); ?></span>
			</a>
		</li>
		<?php endforeach; ?>	
	</ul>
</section>