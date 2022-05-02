/**
 * BLOCK: Google Map
 */


import classnames from "classnames"
import FBFG_Block_Icons from "@Controls/block-icons"
import "./style.scss"
import "./editor.scss"

import { __ } from '@wordpress/i18n';

const {
	registerBlockType
} = wp.blocks

const {
	InspectorControls,
} = wp.blockEditor

const {
	PanelBody,
	RangeControl,
	TextControl,
	SelectControl
} = wp.components

const { Component, Fragment } = wp.element

const api_key = "AIzaSyAsd_d46higiozY-zNqtr7zdA81Soswje4"

class FBFGGoogleMap extends Component {

	constructor() {
		super( ...arguments )
	}

	componentDidMount() {

		// Assigning block_id in the attribute.
		this.props.setAttributes( { block_id: this.props.clientId.substr( 0, 8 ) } )
	}

	render() {

		// Setup the attributes
		const {
			className,
			setAttributes,
			attributes: {
				height,
				zoom,
				address,
				language,
			},
		} = this.props
		let encoded_address = encodeURI( address )

		var lang_par = (language) ? language : "en";

		let url = `https://www.google.com/maps/embed/v1/place?key=${api_key}&q=${encoded_address}&zoom=${zoom}&language=${lang_par}`


		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={ __( "General",'map-block-for-gutenberg' ) } >
						<p className="mbfg-settings-notice">{ __( "This block uses Map Block for Gutenberg's API key to display the map. You don't need to create your own API key or worry about renewing it.",'map-block-for-gutenberg'  ) }</p>
						<p className="components-base-control__label">{__( "Address",'map-block-for-gutenberg'  )}</p>
						<TextControl
							value={ address }
							onChange={ ( value ) => setAttributes( { address: value } ) }
							placeholder={__( "Type the address",'map-block-for-gutenberg'  )}
						/>
						<RangeControl
							label={ __( "Zoom",'map-block-for-gutenberg'  ) }
							value={ zoom }
							onChange={ ( value ) => setAttributes( { zoom: value } ) }
							min={ 1 }
							max={ 22 }
							beforeIcon="editor-textcolor"
							allowReset
						/>
						<RangeControl
							label={ __( "Height",'map-block-for-gutenberg'  ) }
							value={ height }
							onChange={ ( value ) => setAttributes( { height: value } ) }
							min={ 0 }
							max={ 1000 }
							allowReset
						/>
						<SelectControl
						label={ __( "Language",'map-block-for-gutenberg'  ) }
						value={ language }
						onChange={ ( value ) => setAttributes( { language: value } ) }
						options={ [
							{ value: "af",	label: __( "Afrikaans",'map-block-for-gutenberg'  ) },
							{ value: "sq",	label: __( "Albanian",'map-block-for-gutenberg'  ) },
							{ value: "am",	label: __( "Amharic",'map-block-for-gutenberg'  ) },
							{ value: "ar",	label: __( "Arabic",'map-block-for-gutenberg'  ) },
							{ value: "hy",	label: __( "Armenian",'map-block-for-gutenberg'  ) },
							{ value: "az",	label: __( "Azerbaijani",'map-block-for-gutenberg'  ) },
							{ value: "eu",	label: __( "Basque",'map-block-for-gutenberg'  ) },
							{ value: "be",	label: __( "Belarusian",'map-block-for-gutenberg'  ) },
							{ value: "bn",	label: __( "Bengali",'map-block-for-gutenberg'  ) },
							{ value: "bs",	label: __( "Bosnian",'map-block-for-gutenberg'  ) },
							{ value: "bg",	label: __( "Bulgarian",'map-block-for-gutenberg'  ) },
							{ value: "my",	label: __( "Burmese",'map-block-for-gutenberg'  ) },
							{ value: "ca",	label: __( "Catalan",'map-block-for-gutenberg'  ) },
							{ value: "zh",	label: __( "Chinese",'map-block-for-gutenberg'  ) },
							{ value: "hr",	label: __( "Croatian",'map-block-for-gutenberg'  ) },
							{ value: "cs",	label: __( "Czech",'map-block-for-gutenberg'  ) },
							{ value: "da",	label: __( "Danish",'map-block-for-gutenberg'  ) },
							{ value: "nl",	label: __( "Dutch",'map-block-for-gutenberg'  ) },
							{ value: "en",	label: __( "English",'map-block-for-gutenberg'  ) },
							{ value: "et",	label: __( "Estonian",'map-block-for-gutenberg'  ) },
							{ value: "fa",	label: __( "Farsi",'map-block-for-gutenberg'  ) },
							{ value: "fi",	label: __( "Finnish",'map-block-for-gutenberg'  ) },
							{ value: "fr",	label: __( "French",'map-block-for-gutenberg'  ) },
							{ value: "gl",	label: __( "Galician",'map-block-for-gutenberg'  ) },
							{ value: "ka",	label: __( "Georgian",'map-block-for-gutenberg'  ) },
							{ value: "de",	label: __( "German",'map-block-for-gutenberg'  ) },
							{ value: "el",	label: __( "Greek",'map-block-for-gutenberg'  ) },
							{ value: "gu",	label: __( "Gujarati",'map-block-for-gutenberg'  ) },
							{ value: "iw",	label: __( "Hebrew",'map-block-for-gutenberg'  ) },
							{ value: "hi",	label: __( "Hindi",'map-block-for-gutenberg'  ) },
							{ value: "hu",	label: __( "Hungarian",'map-block-for-gutenberg'  ) },
							{ value: "is",	label: __( "Icelandic",'map-block-for-gutenberg'  ) },
							{ value: "id",	label: __( "Indonesian",'map-block-for-gutenberg'  ) },
							{ value: "it",	label: __( "Italian",'map-block-for-gutenberg'  ) },
							{ value: "ja",	label: __( "Japanese",'map-block-for-gutenberg'  ) },
							{ value: "kn",	label: __( "Kannada",'map-block-for-gutenberg'  ) },
							{ value: "kk",	label: __( "Kazakh",'map-block-for-gutenberg'  ) },
							{ value: "km",	label: __( "Khmer",'map-block-for-gutenberg'  ) },
							{ value: "ko",	label: __( "Korean",'map-block-for-gutenberg'  ) },
							{ value: "ky",	label: __( "Kyrgyz",'map-block-for-gutenberg'  ) },
							{ value: "lo",	label: __( "Lao",'map-block-for-gutenberg'  ) },
							{ value: "lv",	label: __( "Latvian",'map-block-for-gutenberg'  ) },
							{ value: "lt",	label: __( "Lithuanian",'map-block-for-gutenberg'  ) },
							{ value: "mk",	label: __( "Macedonian",'map-block-for-gutenberg'  ) },
							{ value: "ms",	label: __( "Malay",'map-block-for-gutenberg'  ) },
							{ value: "ml",	label: __( "Malayalam",'map-block-for-gutenberg' ) },
							{ value: "mr",	label: __( "Marathi",'map-block-for-gutenberg' ) },
							{ value: "mn",	label: __( "Mongolian",'map-block-for-gutenberg' ) },
							{ value: "ne",	label: __( "Nepali",'map-block-for-gutenberg' ) },
							{ value: "no",	label: __( "Norwegian",'map-block-for-gutenberg' ) },
							{ value: "pl",	label: __( "Polish",'map-block-for-gutenberg' ) },
							{ value: "pt",	label: __( "Portuguese",'map-block-for-gutenberg' ) },
							{ value: "pa",	label: __( "Punjabi",'map-block-for-gutenberg' ) },
							{ value: "ro",	label: __( "Romanian",'map-block-for-gutenberg' ) },
							{ value: "ru",	label: __( "Russian",'map-block-for-gutenberg' ) },
							{ value: "sr",	label: __( "Serbian",'map-block-for-gutenberg' ) },
							{ value: "si",	label: __( "Sinhalese",'map-block-for-gutenberg' ) },
							{ value: "sk",	label: __( "Slovak",'map-block-for-gutenberg' ) },
							{ value: "sl",	label: __( "Slovenian",'map-block-for-gutenberg' ) },
							{ value: "es",	label: __( "Spanish",'map-block-for-gutenberg' ) },
							{ value: "sw",	label: __( "Swahili",'map-block-for-gutenberg' ) },
							{ value: "sv",	label: __( "Swedish",'map-block-for-gutenberg' ) },
							{ value: "ta",	label: __( "Tamil",'map-block-for-gutenberg' ) },
							{ value: "te",	label: __( "Telugu",'map-block-for-gutenberg' ) },
							{ value: "th",	label: __( "Thai",'map-block-for-gutenberg' ) },
							{ value: "tr",	label: __( "Turkish",'map-block-for-gutenberg' ) },
							{ value: "uk",	label: __( "Ukrainian",'map-block-for-gutenberg' ) },
							{ value: "ur",	label: __( "Urdu",'map-block-for-gutenberg' ) },
							{ value: "uz",	label: __( "Uzbek",'map-block-for-gutenberg' ) },
							{ value: "vi",	label: __( "Vietnamese",'map-block-for-gutenberg' ) },
							{ value: "zu",	label: __( "Zulu",'map-block-for-gutenberg' ) },							
						] }
						/>
					</PanelBody>
				</InspectorControls>
				<div className={ classnames( className, "mbfg-google-map__wrap", `mbfg-block-${this.props.clientId.substr( 0, 8 )}` ) }>
					<iframe
						className="mbfg-google-map__iframe"
						title = { __( "Google Map for " + address ) }
						src={url}
						style={{height: height}}></iframe>
				</div>
			</Fragment>
		)
	}
}

registerBlockType( "mbfg/google-map", {
	title: mbfg_blocks_info.blocks["mbfg/google-map"]["title"],
	description: mbfg_blocks_info.blocks["mbfg/google-map"]["description"],
	icon: FBFG_Block_Icons.google_map,
	keywords: [
		__( "google map",'map-block-for-gutenberg'  ),
		__( "mbfg",'map-block-for-gutenberg'  ),
		__( "map",'map-block-for-gutenberg'  ),
	],
	supports: {
		anchor: true,
	},
	category: mbfg_blocks_info.category,
	attributes: {
		block_id: {
			type: "string"
		},
		address: {
			type: "string",
			default: "Alamgir"
		},
		height: {
			type: "number",
			default: 300
		},
		zoom: {
			type: "number",
			default: 12
		},
		language: {
			type: "string",
			default: "en",
		},
	},
	example: {},
	edit: FBFGGoogleMap,
	save: function( props ) {

		const {
			block_id,
			height,
			zoom,
			address,
			language
		} = props.attributes
		
		let encoded_address = encodeURI( address )

		var lang_par = (language) ? language : "en";

		let url = `https://www.google.com/maps/embed/v1/place?key=${api_key}&q=${encoded_address}&zoom=${zoom}&language=${lang_par}`

		return (
			<div className={ classnames( props.className, "mbfg-google-map__wrap", `mbfg-block-${block_id}` ) }>
				<iframe
					className="mbfg-google-map__iframe"
					title = { __( "Google Map for " + address ) }
					src={url}
					style={{height: height}}></iframe>
			</div>
		)
	},
	deprecated: [
		{
			attributes : {
				block_id: {
					type: "string"
				},
				address: {
					type: "string",
					default: "Alamgir"
				},
				height: {
					type: "number",
					default: 300
				},
				zoom: {
					type: "number",
					default: 12
				}
			},			
			save: function( props ) {

				const {
					block_id,
					height,
					zoom,
					address
				} = props.attributes
		
				let encoded_address = encodeURI( address )
		
				let url = `https://www.google.com/maps/embed/v1/place?key=${api_key}&q=${encoded_address}&zoom=${zoom}`
		
				return (
					<div className={ classnames( props.className, "mbfg-google-map__wrap", `mbfg-block-${block_id}` ) }>
						<iframe
							className="mbfg-google-map__iframe"
							title = { __( "Google Map for " + address ) }
							src={url}
							style={{height: height}}></iframe>
					</div>
				)
			},
		},
		{
			attributes : {
				block_id: {
					type: "string"
				},
				address: {
					type: "string",
					default: "Mirpur"
				},
				height: {
					type: "number",
					default: 300
				},
				zoom: {
					type: "number",
					default: 12
				}
			},			
			save: function( props ) {
				const {
					block_id,
					height,
					zoom,
					address
				} = props.attributes

				let encoded_address = encodeURI( address )

				let url = `https://www.google.com/maps/embed/v1/place?key=${api_key}&q=${encoded_address}&zoom=${zoom}`

				return (
					<div className={ classnames( props.className, "mbfg-google-map__wrap" ) } id={ `mbfg-google-map-${block_id}`}>
						<iframe
							className="mbfg-google-map__iframe"
							src={url}
							style={{height: height}}></iframe>
					</div>
				)
			},
		},
		{
			attributes : {
				block_id: {
					type: "string"
				},
				address: {
					type: "string",
					default: "Mirpur"
				},
				height: {
					type: "number",
					default: 300
				},
				zoom: {
					type: "number",
					default: 12
				}
			},			
			save: function( props ) {
				const {
					block_id,
					height,
					zoom,
					address
				} = props.attributes
		
				let encoded_address = encodeURI( address )
		
				let url = `https://www.google.com/maps/embed/v1/place?key=${api_key}&q=${encoded_address}&zoom=${zoom}`
		
				return (
					<div className={ classnames( props.className, "mbfg-google-map__wrap", `mbfg-block-${block_id}` ) }>
						<iframe
							className="mbfg-google-map__iframe"
							src={url}
							style={{height: height}}></iframe>
					</div>
				)
			},
		},
	]
} )
