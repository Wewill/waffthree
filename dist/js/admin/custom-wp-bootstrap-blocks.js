/**
 * Adding new colors to column block settings
 */

function waffColumnBgColorOptions( bgColorOptions ) {
	if ( wpBootstrapBlockFilterOptions ) return wpBootstrapBlockFilterOptions;
    return [
		{name:'action-1', 		color:'#9600ff'},
		{name:'action-2', 		color:'#00ff97'},
		{name:'action-3', 		color:'#0032FF'},
		{name:'dark', 			color:'#000000'},
		{name:'light', 			color:'#FFFFFF'},
		{name:'bgcolor', 		color:'hsl(191,15%,93%)'},
		{name:'layoutcolor', 	color:'hsl(0,0%,75%)'},
		{name:'secondary', 		color:'#6c757d'},	
    ];
}

wp.hooks.addFilter(
	'wpBootstrapBlocks.column.bgColorOptions',
	'myplugin/wp-bootstrap-blocks/column/bgColorOptions',
	waffColumnBgColorOptions
);

/**
 * Adding new colors to button block settings
 */

function waffButtonColorOptions( styleOptions ) {
	if ( wpBootstrapBlockFilterOptions ) return wpBootstrapBlockFilterOptions;
	return [
		{ 
			label: 'Action 1', 
			value: 'action-1' 
		},
		{
			label: 'Action 2',
			value: 'action-2',
		},
		{
			label: 'Action 3',
			value: 'action-3',
		},
		{
			label: 'Secondary',
			value: 'secondary',
		},
		{
			label: 'Dark',
			value: 'dark',
		},
		{
			label: 'Light',
			value: 'light',
		},
		{
			label: 'Background color',
			value: 'bgcolor',
		},
		{
			label: 'Layout color',
			value: 'layoutcolor',
		},		
	];
}

wp.hooks.addFilter(
	'wpBootstrapBlocks.button.styleOptions',
	'myplugin/wp-bootstrap-blocks/button/styleOptions',
	waffButtonColorOptions
);