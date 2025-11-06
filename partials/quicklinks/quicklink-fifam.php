<?php

//var_dump( $args );  // Everything
$page_atts = $args;

// Debut Relocate 
global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;

// Do not show quicklinks if no current_edition is set or films are not online
if( empty($current_edition_id) || $current_edition_films_are_online == false ){
	return;
}

echo '##DEBUG';
echo $current_edition_id;


// Navigation links = title + URL + icon (optional) + description (optional) + bg color class (optional) + color class (optional)
// Billeterie, Programmation
$quicklinks = array(
	array(
		'title'       => 'Billetterie',
		'url'         => '/billetterie/',
		'icon'        => 'bi bi-ticket',
		'description' => 'Réservez vos places en ligne afin de ne rien manquer aux projections et événements spéciaux.',
		'bg_color' 	  => 'action-1',
		'link_class' => 'text-action-3 link-action-3 text-hover-action-2',
	),
	array(
		'title'       => 'Programmation',
		'url'         => '/programmation/',
		'icon'        => 'bi bi-collection-play', //-fill
		'description' => 'Découvrez la programmation complète : projections, films, compétition, masterclass, rencontres, invités, débats, rendez-vous étudiants, hors-les-murs et événements spéciaux du festival.',
		'bg_color' 	  => 'action-2',
		'link_class' => 'text-action-3 link-action-3 text-hover-action-1',
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
	<ul class="quicklink-list list-unstyled d-flex flex-column flex-md-row justify-content-center align-items-stretch gap-2 p-4 m-0">
		<?php foreach ( $quicklinks as $link ) : ?>
		<li class="quicklink-item">
			<a 	href="<?php echo esc_url( $link['url'] ); ?>" 
				class="link d-flex flex-row justify-content-center align-items-center gap-2 text-center <?php echo esc_attr( $link['link_class'] ); ?> p-3 rounded-full shadow-lg bg-<?php echo esc_attr( $link['bg_color'] ); ?>" 
				data-bs-toggle="tooltip" data-toggle="tooltip" title="<?= $link['description']; ?>">
				<?php if ( ! empty( $link['icon'] ) ) : ?>
					<i class="<?php echo esc_attr( $link['icon'] ); ?> h5 mb-0" aria-hidden="true"></i>
				<?php endif; ?>
				<span class="quicklink-title h5 mb-0"><?php echo esc_html( $link['title'] ); ?></span>
			</a>
		</li>
		<?php endforeach; ?>	
	</ul>
</section>