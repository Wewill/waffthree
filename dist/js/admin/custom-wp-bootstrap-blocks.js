/**
 * Adding new colors to column block settings
 */

function waffColumnBgColorOptions( bgColorOptions ) {
	if ( wpBootstrapBlockFilterOptions ) return wpBootstrapBlockFilterOptions;
    return [
		{name:'action-1', 		color:'#9600ff'},
		{name:'action-2', 		color:'#00ff97'},
		{name:'action-3', 		color:'#0032FF'},
		{name:'secondary', 		color:'#6c757d'},	
		{name:'dark', 			color:'#000000'},
		{name:'light', 			color:'rgb(248, 249, 250)'},
		{name:'bgcolor', 		color:'hsl(191,15%,93%)'},
		{name:'layoutcolor', 	color:'hsl(0,0%,75%)'},
		{name:'outline-color-main', 		color:'#000000'},
		{name:'outline-action-1', 			color:'#9600ff'},
		{name:'outline-action-2', 			color:'#00ff97'},
		{name:'outline-action-3', 			color:'#0032FF'},
    ];
}

wp.hooks.addFilter(
	'wpBootstrapBlocks.column.bgColorOptions',
	'myplugin/wp-bootstrap-blocks/column/bgColorOptions',
	waffColumnBgColorOptions
);
/**
 * Adding new colors to button block settings
 * Updated to use bgColor and textColor instead of deprecated color attribute
 */

function waffButtonColorOptions( styleOptions ) {
	if ( wpBootstrapBlockFilterOptions ) return wpBootstrapBlockFilterOptions;
	return [
		{
			label: 'Action 1',
			value: 'action-1',
			bgColor: '#9600ff',
			textColor: '#ffffff'
		},
		{
			label: 'Action 2',
			value: 'action-2',
			bgColor: '#00ff97',
			textColor: '#000000'
		},
		{
			label: 'Action 3',
			value: 'action-3',
			bgColor: '#0032FF',
			textColor: '#ffffff'
		},
		{
			label: 'Secondary',
			value: 'secondary',
			bgColor: '#6c757d',
			textColor: '#ffffff'
		},
		{
			label: 'Dark',
			value: 'dark',
			bgColor: '#000000',
			textColor: '#ffffff'
		},
		{
			label: 'Light',
			value: 'light',
			bgColor: 'rgb(248, 249, 250)',
			textColor: '#000000'
		},
		{
			label: 'Background color',
			value: 'bgcolor',
			bgColor: 'hsl(191,15%,93%)',
			textColor: '#000000'
		},
		{
			label: 'Layout color',
			value: 'color-layout',
			bgColor: 'hsl(0,0%,75%)',
			textColor: '#000000'
		},
		{
			label: 'Main color',
			value: 'color-main',
			bgColor: '#000000',
			textColor: '#ffffff'
		},
		{
			label: 'Outline',
			value: 'outline-color-main',
			bgColor: 'transparent',
			textColor: '#000000'
		},
		{
			label: 'Outline action 1',
			value: 'outline-action-1',
			bgColor: 'transparent',
			textColor: '#9600ff'
		},
		{
			label: 'Outline action 2',
			value: 'outline-action-2',
			bgColor: 'transparent',
			textColor: '#00ff97'
		},
		{
			label: 'Outline action 3',
			value: 'outline-action-3',
			bgColor: 'transparent',
			textColor: '#0032FF'
		},
	];
}

wp.hooks.addFilter(
	'wpBootstrapBlocks.button.styleOptions',
	'myplugin/wp-bootstrap-blocks/button/styleOptions',
	waffButtonColorOptions
);