/**
 * Unregistering blocks on dom ready
 * https://github.com/WordPress/gutenberg/issues/25676
 * 
 * OLD > Not used anymore >> See blocks.php
 */
 
wp.domReady(() => {

	/**
	 * blocks
	 */

	var blockCategories = wp.blocks.getCategories();
	console.log(blockCategories);

	var blockTypes = wp.blocks.getBlockTypes()
	console.log(blockTypes);
	console.log(blockTypes.map(b => b.name).join(','));

	// "toolset/ct",
	// "bcn/breadcrumb-trail",
	// "meta-box/wa-latest-posts",
	// "meta-box/wa-partners",
	// "meta-box/wa-edito",
	// "meta-box/wa-contact",
	// "meta-box/wa-playlist",
	// "meta-box/wa-awards",
	// "meta-box/wa-film",
	// "meta-box/wa-section",
	// "meta-box/wa-sections",
	// "meta-box/wa-misson",
	// "meta-box/wa-cols",
	// "meta-box/wa-breaking",
	// "meta-box/wa-insights",
	// "coblocks/accordion",
	// "coblocks/accordion-item",
	// "coblocks/alert",
	// "coblocks/author",
	// "coblocks/gallery-carousel",
	// "coblocks/shape-divider",
	// "coblocks/social",
	// "coblocks/social-profiles",
	// "coblocks/gallery-stacked",
	// "coblocks/posts",
	// "coblocks/post-carousel",
	// "coblocks/map",
	// "coblocks/counter",
	// "coblocks/column",
	// "coblocks/dynamic-separator",
	// "coblocks/events",
	// "coblocks/event-item",
	// "coblocks/faq",
	// "coblocks/faq-item",
	// "coblocks/feature",
	// "coblocks/features",
	// "coblocks/form",
	// "coblocks/field-date",
	// "coblocks/field-email",
	// "coblocks/field-name",
	// "coblocks/field-radio",
	// "coblocks/field-phone",
	// "coblocks/field-textarea",
	// "coblocks/field-text",
	// "coblocks/field-select",
	// "coblocks/field-submit-button",
	// "coblocks/field-checkbox",
	// "coblocks/field-website",
	// "coblocks/field-hidden",
	// "coblocks/click-to-tweet",
	// "coblocks/gallery-collage",
	// "coblocks/food-and-drinks",
	// "coblocks/food-item",
	// "coblocks/logos",
	// "coblocks/gallery-masonry",
	// "coblocks/pricing-table",
	// "coblocks/pricing-table-item",
	// "coblocks/row",
	// "coblocks/service",
	// "coblocks/services",
	// "coblocks/gallery-offset",
	// "coblocks/opentable",
	// "coblocks/icon",
	// "coblocks/gif",
	// "coblocks/gist",
	// "coblocks/hero",
	// "coblocks/highlight",
	// "complianz/document",
	// "complianz/consent-area",
	// "wp-bootstrap-blocks/container",
	// "wp-bootstrap-blocks/column",
	// "wp-bootstrap-blocks/row",
	// "wp-bootstrap-blocks/button",
	// "gravityforms/form",
	// "core/paragraph",
	// "core/image",
	// "core/heading",
	// "core/gallery",
	// "core/list",
	// "core/list-item",
	// "core/quote",
	// "core/archives",
	// "core/audio",
	// "core/button",
	// "core/buttons",
	// "core/calendar",
	// "core/categories",
	// "core/code",
	// "core/column",
	// "core/columns",
	// "core/cover",
	// "core/details",
	// "core/embed",
	// "core/file",
	// "core/group",
	// "core/html",
	// "core/latest-comments",
	// "core/latest-posts",
	// "core/media-text",
	// "core/missing",
	// "core/more",
	// "core/nextpage",
	// "core/page-list",
	// "core/page-list-item",
	// "core/pattern",
	// "core/preformatted",
	// "core/pullquote",
	// "core/block",
	// "core/rss",
	// "core/search",
	// "core/separator",
	// "core/shortcode",
	// "core/social-link",
	// "core/social-links",
	// "core/spacer",
	// "core/table",
	// "core/tag-cloud",
	// "core/text-columns",
	// "core/verse",
	// "core/video",
	// "core/footnotes",
	// "core/navigation",
	// "core/navigation-link",
	// "core/navigation-submenu",
	// "core/site-logo",
	// "core/site-title",
	// "core/site-tagline",
	// "core/query",
	// "core/template-part",
	// "core/avatar",
	// "core/post-title",
	// "core/post-excerpt",
	// "core/post-featured-image",
	// "core/post-content",
	// "core/post-author",
	// "core/post-author-name",
	// "core/post-date",
	// "core/post-terms",
	// "core/post-navigation-link",
	// "core/post-template",
	// "core/query-pagination",
	// "core/query-pagination-next",
	// "core/query-pagination-numbers",
	// "core/query-pagination-previous",
	// "core/query-no-results",
	// "core/read-more",
	// "core/comments",
	// "core/comment-author-name",
	// "core/comment-content",
	// "core/comment-date",
	// "core/comment-edit-link",
	// "core/comment-reply-link",
	// "core/comment-template",
	// "core/comments-title",
	// "core/comments-pagination",
	// "core/comments-pagination-next",
	// "core/comments-pagination-numbers",
	// "core/comments-pagination-previous",
	// "core/post-comments-form",
	// "core/home-link",
	// "core/loginout",
	// "core/term-description",
	// "core/query-title",
	// "core/post-author-biography",
	// "core/freeform",
	// "core/legacy-widget",
	// "core/widget-group",
	// "coblocks/buttons",
	// "coblocks/media-card"

	/**
	 * core/text
	 */

	//core/code
	//core/preformatted

	/**
	 * core/media
	 */


	/**
	 * core/design
	 */

	// coblocks/shape-divider 
	// coblocks/form
	// coblocks/food-and-drinks
	// coblocks/food-item
	// coblocks/pricing-table
	// coblocks/pricing-table-item
	// coblocks/opentable


	/**
	 * toolset
	 */

	//All

	var unallowedBlocks = [
		'core/code',
		'core/preformatted',
		'core/freeform',
		//
		'coblocks/shape-divider',
		'coblocks/form',
		'coblocks/food-and-drinks',
		'coblocks/food-item',
		'coblocks/pricing-table',
		'coblocks/pricing-table-item',
		'coblocks/opentable',
		//
		'complianz/document',
		//
		'core/archives',
		'bcn/breadcrumb-trail',
		'coblocks/social', 
		'coblocks/social-profiles', 
		'coblocks/counter',
		'core/calendar',
		//'core/html', #43
		'core/latest-comments',
		'core/page-list',
		'core/rss',
		'core/social-link',
		'core/social-links',
		'core/tag-cloud',
	];
	
	wp.blocks.getBlockTypes().forEach( function ( blockType ) {
		// Unregister all unallowed blocks
		console.log("getBlockTypes()", unallowedBlocks.indexOf( blockType.name ), blockType.name, blockType.category);

		if ( unallowedBlocks.indexOf( blockType.name ) !== -1 ) {
			wp.blocks.unregisterBlockType( blockType.name );
		}

		// Unregister all toolset blocks
		if ( blockType.category === 'toolset' ) {
			wp.blocks.unregisterBlockType( blockType.name );
		}

		// Unregister all toolset views blocks
		if ( blockType.category === 'toolset-views' ) {
			wp.blocks.unregisterBlockType( blockType.name );
		}

		// Unregister all theme blocks
		// if ( blockType.category === 'theme' ) {
		// 	wp.blocks.unregisterBlockType( blockType.name );
		// }

		// Unregister all widgets blocks
		// if ( blockType.category === 'widgets' ) {
		// 	console.log('widgets::', blockType.name)
		// }

	} );


	/**
	 * core/embed
	 */

	// Allowed blocks 
	var enabledEmbeds = [ 'twitter', 'youtube', 'facebook', 'instagram', 'flickr', 'vimeo', 'issuu', 'tiktok'];
	var embedBlock = wp.blocks.getBlockVariations('core/embed');
	// console.log(embedBlock);

	// Unregister blocks
	if (embedBlock) {
		embedBlock.forEach(function(el) {
			if (!enabledEmbeds.includes(el.name)) {
				wp.blocks.unregisterBlockVariation('core/embed', el.name);
			}
		})
	}



});