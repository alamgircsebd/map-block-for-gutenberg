import { __ } from '@wordpress/i18n';

const gutterOptions = [
	{
		value: '0',
		label: __( 'None','map-block-for-gutenberg' ),
		shortName: __( 'None','map-block-for-gutenberg' ),
	},
	{
		value: '5',
		/* translators: abbreviation for small size */
		label: __( 'S','map-block-for-gutenberg' ),
		tooltip: __( 'Small','map-block-for-gutenberg' ),
	},
	{
		value: '10',
		/* translators: abbreviation for medium size */
		label: __( 'M','map-block-for-gutenberg' ),
		tooltip: __( 'Medium','map-block-for-gutenberg' ),
	},
	{
		value: '15',
		/* translators: abbreviation for large size */
		label: __( 'L','map-block-for-gutenberg' ),
		tooltip: __( 'Large','map-block-for-gutenberg' ),
	},
	{
		value: '20',
		/* translators: abbreviation for largest size */
		label: __( 'XL','map-block-for-gutenberg' ),
		tooltip: __( 'Huge','map-block-for-gutenberg' ),
	},
];

export default gutterOptions;