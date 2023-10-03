/**
 * Unregistering blocks on dom ready
 * https://github.com/WordPress/gutenberg/issues/25676
 */
 
 wp.domReady(() => {

	/**
	 * blocks
	 */

	var blockCategories = wp.blocks.getCategories();
	console.log(blockCategories);

	var blockTypes = wp.blocks.getBlockTypes()
	console.log(blockTypes);

	// 	[Log] Array (191) (custom-wp-bootstrap-reset.js, line 16)
	// 0 {name: "toolset/ct", icon: Object, keywords: ["Toolset", "Content Template", "Shortcode"], attributes: Object, providesContext: {}, …}
	// 1 {name: "toolset/cred-form", icon: Object, keywords: ["Toolset", "Form", "Shortcode"], attributes: Object, providesContext: {}, …}
	// 2 {name: "bcn/breadcrumb-trail", icon: Object, keywords: [], attributes: {lock: {type: "object"}, className: {type: "string"}, animation: {type: "string"}}, providesContext: {}, …}
	// 3 {name: "toolset-views/custom-search-container", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 4 {name: "toolset-views/custom-search-filter", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 5 {name: "toolset-views/custom-search-reset", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 6 {name: "toolset-views/custom-search-submit", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 7 {name: "toolset-views/view-editor", icon: Object, keywords: ["Toolset", "View", "Shortcode"], attributes: Object, providesContext: {}, …}
	// 8 {name: "toolset-views/view-template-block", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 9 {name: "toolset-views/view-pagination-block", icon: Object, keywords: ["pagination", "dots links next last page button total pages current dropdown"], attributes: Object, providesContext: {}, …}
	// 10 {name: "toolset-views/sorting", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 11 {name: "toolset-views/table-column", icon: {src: "lock"}, keywords: [], attributes: {verticalAlignment: {type: "string", default: ""}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 12 {name: "toolset-views/table-header-column", icon: {src: "lock"}, keywords: [], attributes: {verticalAlignment: {type: "string", default: ""}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 13 {name: "toolset-views/table-header-row", icon: {src: "lock"}, keywords: [], attributes: {lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 14 {name: "toolset-views/table-row", icon: {src: "lock"}, keywords: [], attributes: {columns: {type: "number", default: 2}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 15 {name: "toolset-views/view-layout-block", icon: Object, keywords: [], attributes: {recreated: {type: "boolean", default: false}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 16 {name: "toolset/map", icon: Object, keywords: ["Toolset", "map", "shortcode"], attributes: Object, providesContext: {}, …}
	// 17 {name: "meta-box/wa-lastest-posts", icon: {foreground: "#9500ff", src: "format-standard"}, keywords: ["lastest", "posts", "blog", "articles"], attributes: Object, providesContext: {}, …}
	// 18 {name: "meta-box/wa-partners", icon: {foreground: "#9500ff", src: "networking"}, keywords: ["partners", "posts", "partner", "logotype"], attributes: Object, providesContext: {}, …}
	// 19 {name: "meta-box/wa-edito", icon: {foreground: "#9500ff", src: "format-quote"}, keywords: ["edito", "post", "text"], attributes: Object, providesContext: {}, …}
	// 20 {name: "meta-box/wa-contact", icon: {foreground: "#9500ff", src: "text-page"}, keywords: ["contact", "post", "text"], attributes: Object, providesContext: {}, …}
	// 21 {name: "meta-box/wa-awards", icon: {foreground: "#9500ff", src: "admin-site"}, keywords: Array, attributes: Object, providesContext: {}, …}
	// 22 {name: "meta-box/wa-playlist", icon: {foreground: "#9500ff", src: "video-alt3"}, keywords: Array, attributes: Object, providesContext: {}, …}
	// 23 {name: "meta-box/wa-film", icon: {foreground: "#9500ff", src: "video-alt"}, keywords: ["film", "posts", "single"], attributes: Object, providesContext: {}, …}
	// 24 {name: "meta-box/wa-section", icon: {foreground: "#9500ff", src: "images-alt"}, keywords: ["film", "section", "category"], attributes: Object, providesContext: {}, …}
	// 25 {name: "coblocks/accordion", icon: Object, keywords: ["coblocks", "tabs", "faq"], attributes: Object, providesContext: {}, …}
	// 26 {name: "coblocks/accordion-item", icon: Object, keywords: ["coblocks", "tabs", "faq"], attributes: Object, providesContext: {}, …}
	// 27 {name: "coblocks/alert", icon: Object, keywords: ["coblocks", "notice", "message"], attributes: Object, providesContext: {}, …}
	// 28 {name: "coblocks/author", icon: Object, keywords: ["coblocks", "biography", "profile"], attributes: Object, providesContext: {}, …}
	// 29 {name: "coblocks/gallery-carousel", icon: Object, keywords: ["coblocks", "gallery", "photos"], attributes: Object, providesContext: {}, …}
	// 30 {name: "coblocks/shape-divider", icon: Object, keywords: ["coblocks", "hr", "svg", "separator"], attributes: Object, providesContext: {}, …}
	// 31 {name: "coblocks/social", icon: {src: "block-default"}, keywords: [], attributes: Object, providesContext: {}, …}
	// 32 {name: "coblocks/social-profiles", icon: Object, keywords: ["coblocks", "share", "links", "icons"], attributes: Object, providesContext: {}, …}
	// 33 {name: "coblocks/gallery-stacked", icon: Object, keywords: ["coblocks", "gallery", "photos", "lightbox"], attributes: Object, providesContext: {}, …}
	// 34 {name: "coblocks/posts", icon: Object, keywords: ["coblocks", "posts", "blog", "latest", "rss"], attributes: Object, providesContext: {}, …}
	// 35 {name: "coblocks/post-carousel", icon: Object, keywords: Array, attributes: Object, providesContext: {}, …}
	// 36 {name: "coblocks/map", icon: Object, keywords: ["coblocks", "address", "maps", "google", "directions"], attributes: Object, providesContext: {}, …}
	// 37 {name: "coblocks/counter", icon: Object, keywords: ["numbers", "stats"], attributes: Object, providesContext: {}, …}
	// 38 {name: "coblocks/column", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 39 {name: "coblocks/dynamic-separator", icon: Object, keywords: ["coblocks", "hr", "spacer", "separator"], attributes: Object, providesContext: {}, …}
	// 40 {name: "coblocks/events", icon: Object, keywords: ["coblocks", "calendar", "date"], attributes: Object, providesContext: {}, …}
	// 41 {name: "coblocks/event-item", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 42 {name: "coblocks/faq", icon: Object, keywords: ["coblocks", "FAQ", "Frequently asked questions"], attributes: Object, providesContext: {}, …}
	// 43 {name: "coblocks/faq-item", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 44 {name: "coblocks/feature", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 45 {name: "coblocks/features", icon: Object, keywords: ["coblocks"], attributes: Object, providesContext: {}, …}
	// 46 {name: "coblocks/click-to-tweet", icon: Object, keywords: ["coblocks", "share", "twitter"], attributes: Object, providesContext: {}, …}
	// 47 {name: "coblocks/gallery-collage", icon: Object, keywords: ["coblocks", "gallery", "photos"], attributes: Object, providesContext: {}, …}
	// 48 {name: "coblocks/field-date", icon: Object, keywords: ["coblocks", "calendar", "day", "month", "year"], attributes: Object, providesContext: {}, …}
	// 49 {name: "coblocks/field-email", icon: Object, keywords: ["coblocks", "e-mail", "mail"], attributes: Object, providesContext: {}, …}
	// 50 {name: "coblocks/field-name", icon: Object, keywords: ["coblocks", "email", "first name", "last name"], attributes: Object, providesContext: {}, …}
	// 51 {name: "coblocks/field-radio", icon: Object, keywords: ["coblocks", "choose", "select", "option"], attributes: Object, providesContext: {}, …}
	// 52 {name: "coblocks/field-phone", icon: Object, keywords: Array, attributes: Object, providesContext: {}, …}
	// 53 {name: "coblocks/field-textarea", icon: Object, keywords: ["coblocks", "text", "message text", "multiline text"], attributes: Object, providesContext: {}, …}
	// 54 {name: "coblocks/field-text", icon: Object, keywords: ["coblocks", "text control", "text box", "input"], attributes: Object, providesContext: {}, …}
	// 55 {name: "coblocks/field-select", icon: Object, keywords: ["coblocks", "dropdown", "option"], attributes: Object, providesContext: {}, …}
	// 56 {name: "coblocks/field-submit-button", icon: Object, keywords: ["coblocks", "submit", "button"], attributes: Object, providesContext: {}, …}
	// 57 {name: "coblocks/field-checkbox", icon: Object, keywords: ["coblocks", "option"], attributes: Object, providesContext: {}, …}
	// 58 {name: "coblocks/field-website", icon: Object, keywords: ["coblocks", "link", "hyperlink", "url"], attributes: Object, providesContext: {}, …}
	// 59 {name: "coblocks/field-hidden", icon: Object, keywords: ["coblocks", "input", "text"], attributes: Object, providesContext: {}, …}
	// 60 {name: "coblocks/food-and-drinks", icon: Object, keywords: ["coblocks", "restaurant", "menu"], attributes: Object, providesContext: {}, …}
	// 61 {name: "coblocks/food-item", icon: Object, keywords: ["coblocks", "menu"], attributes: Object, providesContext: {}, …}
	// 62 {name: "coblocks/form", icon: Object, keywords: ["coblocks", "email", "about", "contact"], attributes: Object, providesContext: {}, …}
	// 63 {name: "coblocks/logos", icon: Object, keywords: Array, attributes: Object, providesContext: {}, …}
	// 64 {name: "coblocks/gallery-masonry", icon: Object, keywords: ["images", "photos"], attributes: Object, providesContext: {allowResize: "allowResize", imageCrop: "imageCrop"}, …}
	// 65 {name: "coblocks/pricing-table", icon: Object, keywords: ["coblocks", "landing", "comparison"], attributes: Object, providesContext: {}, …}
	// 66 {name: "coblocks/pricing-table-item", icon: Object, keywords: ["coblocks", "landing", "comparison"], attributes: Object, providesContext: {}, …}
	// 67 {name: "coblocks/row", icon: Object, keywords: ["coblocks", "rows", "columns", "layouts"], attributes: Object, providesContext: {}, …}
	// 68 {name: "coblocks/service", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 69 {name: "coblocks/services", icon: Object, keywords: ["coblocks", "features"], attributes: Object, providesContext: {}, …}
	// 70 {name: "coblocks/gallery-offset", icon: Object, keywords: ["coblocks", "gallery", "photos"], attributes: Object, providesContext: {}, …}
	// 71 {name: "coblocks/opentable", icon: Object, keywords: ["coblocks", "restaurant", "reservation", "open", "table"], attributes: Object, providesContext: {}, …}
	// 72 {name: "coblocks/icon", icon: Object, keywords: ["coblocks", "svg", "icons"], attributes: Object, providesContext: {}, …}
	// 73 {name: "coblocks/gif", icon: Object, keywords: ["coblocks", "animated"], attributes: Object, providesContext: {}, …}
	// 74 {name: "coblocks/gist", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 75 {name: "coblocks/hero", icon: Object, keywords: ["coblocks", "button", "cta", "call to action"], attributes: Object, providesContext: {}, …}
	// 76 {name: "coblocks/highlight", icon: Object, keywords: ["coblocks", "text", "paragraph"], attributes: Object, providesContext: {}, …}
	// 77 {name: "complianz/document", icon: Object, keywords: ["Déclaration de confidentialité", "Politique de cookies", "Avertissement"], attributes: Object, providesContext: {}, …}
	// 78 {name: "toolset-blocks/audio", icon: Object, keywords: ["audio", "audio", "toolset"], attributes: Object, providesContext: {}, …}
	// 79 {name: "toolset-blocks/button", icon: Object, keywords: ["button", "toolset", "blocks"], attributes: Object, providesContext: {}, …}
	// 80 {name: "toolset-blocks/conditional", icon: Object, keywords: ["condition", "toolset", "blocks"], attributes: Object, providesContext: {}, …}
	// 81 {name: "toolset-blocks/container", icon: Object, keywords: ["container", "group", "toolset"], attributes: Object, providesContext: {}, …}
	// 82 {name: "toolset-blocks/countdown", icon: Object, keywords: ["countdown", "Blocks", "Toolset"], attributes: Object, providesContext: {}, …}
	// 83 {name: "toolset-blocks/fields-and-text", icon: Object, keywords: ["html", "text", "toolset"], attributes: Object, providesContext: {}, …}
	// 84 {name: "toolset-blocks/field", icon: Object, keywords: ["field", "toolset", "blocks"], attributes: Object, providesContext: {}, …}
	// 85 {name: "toolset-blocks/gallery", icon: Object, keywords: Array, attributes: Object, providesContext: {}, …}
	// 86 {name: "toolset-blocks/grid", icon: Object, keywords: ["Toolset", "Grid", "Column", "Columns"], attributes: Object, providesContext: {}, …}
	// 87 {name: "toolset-blocks/grid-column", icon: Object, keywords: ["Toolset", "Grid Column"], attributes: Object, providesContext: {}, …}
	// 88 {name: "toolset-blocks/heading", icon: Object, keywords: ["heading", "toolset", "blocks"], attributes: Object, providesContext: {}, …}
	// 89 {name: "toolset-blocks/image-slider", icon: Object, keywords: Array, attributes: Object, providesContext: {}, …}
	// 90 {name: "toolset-blocks/image", icon: Object, keywords: ["img", "photo", "toolset"], attributes: Object, providesContext: {}, …}
	// 91 {name: "toolset-blocks/progress", icon: Object, keywords: ["progress", "indicator", "Toolset"], attributes: Object, providesContext: {}, …}
	// 92 {name: "toolset-blocks/repeating-field", icon: Object, keywords: ["field", "toolset", "blocks"], attributes: Object, providesContext: {}, …}
	// 93 {name: "toolset-blocks/social-share", icon: Object, keywords: ["Social", "Share", "Toolset"], attributes: Object, providesContext: {}, …}
	// 94 {name: "toolset-blocks/star-rating", icon: Object, keywords: ["star-rating", "toolset", "blocks"], attributes: Object, providesContext: {}, …}
	// 95 {name: "toolset-blocks/video", icon: Object, keywords: ["video", "movie", "toolset"], attributes: Object, providesContext: {}, …}
	// 96 {name: "toolset-blocks/youtube", icon: Object, keywords: ["youtube", "video", "toolset"], attributes: Object, providesContext: {}, …}
	// 97 {name: "gravityforms/form", icon: Object, keywords: ["gravity forms", "newsletter", "contact"], attributes: Object, providesContext: {}, …}
	// 98 {name: "wp-bootstrap-blocks/container", icon: Object, keywords: ["Container", "Bootstrap Container", "Bootstrap"], attributes: Object, providesContext: {}, …}
	// 99 {name: "wp-bootstrap-blocks/column", icon: Object, keywords: ["Column", "Bootstrap Column", "Bootstrap"], attributes: Object, providesContext: {}, …}
	// 100 {name: "wp-bootstrap-blocks/row", icon: Object, keywords: ["Row", "Bootstrap Row", "Bootstrap"], attributes: Object, providesContext: {}, …}
	// 101 {name: "wp-bootstrap-blocks/button", icon: Object, keywords: ["Button", "Bootstrap Button", "Bootstrap"], attributes: Object, providesContext: {}, …}
	// 102 {name: "core/paragraph", icon: Object, keywords: ["texte"], attributes: Object, providesContext: {}, …}
	// 103 {name: "core/image", icon: Object, keywords: ["img", "photo", "visuel"], attributes: Object, providesContext: {}, …}
	// 104 {name: "core/heading", icon: Object, keywords: ["titre", "sous-titre"], attributes: Object, providesContext: {}, …}
	// 105 {name: "core/gallery", icon: Object, keywords: ["images", "photos"], attributes: Object, providesContext: {allowResize: "allowResize", imageCrop: "imageCrop", fixedHeight: "fixedHeight"}, …}
	// 106 {name: "core/list", icon: Object, keywords: ["liste à puces", "liste ordonnée", "liste numérotée"], attributes: Object, providesContext: {}, …}
	// 107 {name: "core/quote", icon: Object, keywords: ["bloc de citation", "citation"], attributes: Object, providesContext: {}, …}
	// 108 {name: "core/archives", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 109 {name: "core/audio", icon: Object, keywords: ["musique", "son", "podcast", "enregistrement"], attributes: Object, providesContext: {}, …}
	// 110 {name: "core/button", icon: Object, keywords: ["lien"], attributes: Object, providesContext: {}, …}
	// 111 {name: "core/buttons", icon: Object, keywords: ["lien"], attributes: Object, providesContext: {}, …}
	// 112 {name: "core/calendar", icon: Object, keywords: ["articles", "archive"], attributes: Object, providesContext: {}, …}
	// 113 {name: "core/categories", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 114 {name: "core/freeform", icon: Object, keywords: [], attributes: {content: {type: "string", source: "html"}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 115 {name: "core/code", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 116 {name: "core/column", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 117 {name: "core/columns", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 118 {name: "core/cover", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 119 {name: "core/embed", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 120 {name: "core/file", icon: Object, keywords: ["document", "pdf", "téléchargement"], attributes: Object, providesContext: {}, …}
	// 121 {name: "core/group", icon: Object, keywords: ["contenant", "conteneur", "ligne", "section"], attributes: Object, providesContext: {}, …}
	// 122 {name: "core/html", icon: Object, keywords: ["embed"], attributes: {content: {type: "string", source: "html"}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 123 {name: "core/latest-comments", icon: Object, keywords: ["commentaires récents"], attributes: Object, providesContext: {}, …}
	// 124 {name: "core/latest-posts", icon: Object, keywords: ["publications récentes"], attributes: Object, providesContext: {}, …}
	// 125 {name: "core/media-text", icon: Object, keywords: ["image", "vidéo"], attributes: Object, providesContext: {}, …}
	// 126 {name: "core/missing", icon: {src: "block-default"}, keywords: [], attributes: Object, providesContext: {}, …}
	// 127 {name: "core/more", icon: Object, keywords: ["lire la suite"], attributes: Object, providesContext: {}, …}
	// 128 {name: "core/nextpage", icon: Object, keywords: ["page suivante", "pagination"], attributes: {lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 129 {name: "core/page-list", icon: Object, keywords: ["menu", "navigation"], attributes: Object, providesContext: {}, …}
	// 130 {name: "core/pattern", icon: {src: "block-default"}, keywords: [], attributes: Object, providesContext: {}, …}
	// 131 {name: "core/preformatted", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 132 {name: "core/pullquote", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 133 {name: "core/block", icon: Object, keywords: [], attributes: {ref: {type: "number"}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 134 {name: "core/rss", icon: Object, keywords: ["atom", "flux"], attributes: Object, providesContext: {}, …}
	// 135 {name: "core/search", icon: Object, keywords: ["trouver"], attributes: Object, providesContext: {}, …}
	// 136 {name: "core/separator", icon: Object, keywords: ["Filet horizontal", "hr", "Séparateur"], attributes: Object, providesContext: {}, …}
	// 137 {name: "core/shortcode", icon: Object, keywords: [], attributes: {text: {type: "string", source: "html"}, lock: {type: "object"}, animation: {type: "string"}}, providesContext: {}, …}
	// 138 {name: "core/social-link", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 139 {name: "core/social-links", icon: Object, keywords: ["liens"], attributes: Object, providesContext: Object, …}
	// 140 {name: "core/spacer", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 141 {name: "core/table", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 142 {name: "core/tag-cloud", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 143 {name: "core/text-columns", icon: {src: "columns"}, keywords: [], attributes: Object, providesContext: {}, …}
	// 144 {name: "core/verse", icon: Object, keywords: ["poésie", "poème"], attributes: Object, providesContext: {}, …}
	// 145 {name: "core/video", icon: Object, keywords: ["vidéo"], attributes: Object, providesContext: {}, …}
	// 146 {name: "core/navigation", icon: Object, keywords: ["menu", "navigation", "liens"], attributes: Object, providesContext: Object, …}
	// 147 {name: "core/navigation-link", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 148 {name: "core/navigation-submenu", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 149 {name: "core/site-logo", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 150 {name: "core/site-title", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 151 {name: "core/site-tagline", icon: Object, keywords: ["description"], attributes: Object, providesContext: {}, …}
	// 152 {name: "core/query", icon: Object, keywords: [], attributes: Object, providesContext: {queryId: "queryId", query: "query", displayLayout: "displayLayout"}, …}
	// 153 {name: "core/template-part", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 154 {name: "core/avatar", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 155 {name: "core/post-title", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 156 {name: "core/post-excerpt", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 157 {name: "core/post-featured-image", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 158 {name: "core/post-content", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 159 {name: "core/post-author", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 160 {name: "core/post-date", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 161 {name: "core/post-terms", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 162 {name: "core/post-navigation-link", icon: {src: "block-default"}, keywords: [], attributes: Object, providesContext: {}, …}
	// 163 {name: "core/post-template", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 164 {name: "core/query-pagination", icon: Object, keywords: [], attributes: Object, providesContext: {paginationArrow: "paginationArrow"}, …}
	// 165 {name: "core/query-pagination-next", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 166 {name: "core/query-pagination-numbers", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 167 {name: "core/query-pagination-previous", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 168 {name: "core/query-no-results", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 169 {name: "core/read-more", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 170 {name: "core/comment-author-name", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 171 {name: "core/comment-content", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 172 {name: "core/comment-date", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 173 {name: "core/comment-edit-link", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 174 {name: "core/comment-reply-link", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 175 {name: "core/comment-template", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 176 {name: "core/comments-title", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 177 {name: "core/comments-query-loop", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 178 {name: "core/comments-pagination", icon: Object, keywords: [], attributes: Object, providesContext: {comments/paginationArrow: "paginationArrow"}, …}
	// 179 {name: "core/comments-pagination-next", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 180 {name: "core/comments-pagination-numbers", icon: Object, keywords: [], attributes: {lock: {type: "object"}, className: {type: "string"}, animation: {type: "string"}}, providesContext: {}, …}
	// 181 {name: "core/comments-pagination-previous", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 182 {name: "core/post-comments", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 183 {name: "core/post-comments-form", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 184 {name: "core/home-link", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 185 {name: "core/loginout", icon: Object, keywords: ["connexion", "déconnexion", "formulaire"], attributes: Object, providesContext: {}, …}
	// 186 {name: "core/term-description", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 187 {name: "core/query-title", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 188 {name: "core/post-author-biography", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}
	// 189 {name: "coblocks/buttons", icon: Object, keywords: ["coblocks", "link", "cta", "call to action"], attributes: Object, providesContext: {}, …}
	// 190 {name: "coblocks/media-card", icon: Object, keywords: [], attributes: Object, providesContext: {}, …}


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
		if ( blockType.category === 'theme' ) {
			wp.blocks.unregisterBlockType( blockType.name );
		}

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