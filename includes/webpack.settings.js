/* global module */

// Webpack settings exports.
module.exports = {
	entries: {
		'design-styles/style-yourchildtheme': './.dev/assets/design-styles/yourchildtheme/css/style-yourchildtheme.css',
		'design-styles/style-yourchildtheme-editor': './.dev/assets/design-styles/yourchildtheme/css/style-yourchildtheme-editor.css'
	},
	paths: {
		src: {
			base: './.dev/assets/',
			yourchildthemeBase: './.dev/assets/design-styles/yourchildtheme/',
			yourchildthemeCss: './.dev/assets/design-styles/yourchildtheme/css/'
		},
		dist: {
			base: './dist/',
			clean: ['./images', './css', './js']
		},
	},
	stats: {
		all: false,
		errors: true,
		maxModules: 0,
		modules: true,
		warnings: true,
		assets: true,
		errorDetails: true,
		excludeAssets: /\.(jpe?g|png|gif|svg|woff|woff2|ttf)$/i,
		moduleTrace: true,
		performance: true
	},
	copyWebpackConfig: {
		from: '.dev/assets/**/*.{jpg,jpeg,png,gif,svg}',
		to: 'images/[path][name].[ext]',
		transformPath: ( targetPath ) => {
			return 'images/' + targetPath.replace( /(\.dev\/assets\/|images\/|shared\/)/g, '' );
		},
	},
	BrowserSyncConfig: {
		host: 'localhost',
		port: 3000,
		proxy: 'https://go.test',
		open: true,
		files: [
			'**/*.php',
			'dist/js/**/*.js',
			'dist/css/**/*.css',
			'dist/images/**/*.{jpg,jpeg,png,gif,svg}'
		]
	},
	performance: {
		maxAssetSize: 100000
	},
};
