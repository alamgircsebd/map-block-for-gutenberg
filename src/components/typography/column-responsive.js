/**
 * WordPress dependencies
 */
 import { __ } from '@wordpress/i18n';

const {
	ButtonGroup,
	Button,
	Dashicon,
} = wp.components

// Extend component
const { Fragment } = wp.element
const { useSelect, useDispatch } = wp.data;
import map from 'lodash/map';

/**
 * Build the Measure controls
 * @returns {object} Measure settings.
 */
export default function Columnresponsive ( props ) {
	const deviceType = useSelect( ( select ) => {
		return select( 'core/edit-post' ).__experimentalGetPreviewDeviceType();
    }, [] );
	const {
		__experimentalSetPreviewDeviceType: setPreviewDeviceType,
	} = useDispatch( 'core/edit-post' );
	const customSetPreviewDeviceType = ( device ) => {
		setPreviewDeviceType( device );
    };
	const devices = [
		{
			name: 'Desktop',
			title: <Dashicon icon="desktop" />,
			itemClass: 'mbfg-desktop-tab mbfg-responsive-tabs',
		},
		{
			name: 'Tablet',
			title: <Dashicon icon="tablet" />,
			itemClass: 'mbfg-tablet-tab mbfg-responsive-tabs',
		},
		{
			name: 'Mobile',
			key: 'mobile',
			title: <Dashicon icon="smartphone" />,
			itemClass: 'mbfg-mobile-tab mbfg-responsive-tabs',
		},
	];
	const output = {};
	output.Desktop = (
        <Fragment></Fragment>
	);
	output.Tablet = (
        <Fragment></Fragment>
	);
	output.Mobile = (
        <Fragment></Fragment>
	);
	return (
		<div className={ 'mbfg-typography-range-options' }>
			<div className="mbfg-size-type-field-tabs">
				<ButtonGroup className="components-tab-panel__tabs" aria-label={ __( 'Device', 'map-block-for-gutenberg' ) }>
					{ map( devices, ( { name, key, title, itemClass } ) => (
						<Button
							key={ key }
							className={ `components-button components-tab-panel__tabs-item ${ itemClass }${ name === deviceType ? ' active-tab' : '' }` }
							aria-pressed={ deviceType === name }
							onClick={ () => customSetPreviewDeviceType( name ) }
						>
							{ title }
						</Button>
					) ) }
				</ButtonGroup>
				<div className="mbfg-responsive-control-inner">
				{ ( output[ deviceType ] ? output[ deviceType ] : output.Desktop ) }
				</div>
			</div>
		</div>
	);
}