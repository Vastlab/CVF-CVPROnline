/**
 * Block dependencies
 */

import EctIcon from './icons';
import LayoutType from './layout-type'
/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const baseURL=ectUrl;
const LayoutImgPath=baseURL+'admin/gutenberg-block/layout-images';
const { apiFetch } = wp;
const {
	RichText,
	InspectorControls,
	BlockControls,
} = wp.editor;

const { 
	PanelBody,
	PanelRow,
	TextareaControl,
	TextControl,
	Dashicon,
	Toolbar,
	Button,
	SelectControl,
	Tooltip,
	RangeControl,
} = wp.components;
let categoryList = [];

wp.apiFetch({path:'/tribe/events/v1/categories/?per_page=50'}).then(data => {
 if(typeof(data.categories)!=undefined){
	categoryList=data.categories.map(function(val,key){
		return {label: val.name, value: val.slug};
	});
	categoryList.push({label: "Select a Category", value:'all'});
	}
});

/**
 * Register block
 */
export default registerBlockType( 'ect/shortcode', {
		// Block Title
		title: __( 'Events Calendar Shortcode' ),
		// Block Description
		description: __( 'The Events Calendar - Shortcode & Templates' ),
		// Block Category
		category: 'common',
		// Block Icon
		icon: EctIcon,
		// Block Keywords
		keywords: [
			__( 'the events calendar' ),
			__( 'templates' ),
			__( 'cool plugins' )
		],
	attributes: {
		template: {
			type: 'string',
			default: 'default'
		},
		category: {
			type: 'string',
			default: 'all'
		},
		style: {
			type: 'string',
			default: 'style-1'
		},
		order: {
			type: 'string',
			default: 'ASC'
		},
		based: {
			type: 'string',
			default: 'default'
		},
		storycontent: {
			type: 'string',
			default: 'default'
		},
		limit: {
            type: 'string',
            default: '10'
        },
		dateformat: {
			type: 'string',
			default:  'default',
		},
		startDate: {
            type: 'string',
            default: ''
		},
		endDate: {
            type: 'string',
            default: ''
        },
		hideVenue: {
			type: 'string',
			default:  'no',
		},
		time: {
			type: 'string',
			default:  'future',
		},
		socialshare: {
			type: 'string',
			default: 'no'
		}
	},
	// Defining the edit interface
	edit: props => {
		
		const layoutOptions = [
			{label: 'Default List Layout', value: 'default'},
			{label: 'Timeline Layout', value: 'timeline-view'},		
		];
		const designsOptions = [
			{label: 'Style 1', value: 'style-1'},
			{label: 'Style 2', value: 'style-2'},
			{label: 'Style 3', value: 'style-3'}		
		];
		const dateFormatsOptions = [
			{label:"Default (01 January 2019)",value:"default"},
			{label:"Md,Y (Jan 01, 2019)",value:"MD,Y"},
			{label:"Fd,Y (January 01, 2019)",value:"FD,Y"},
			{label:"dM (01 Jan)",value:"DM"},
			{label:"dML (01 Jan Monday)",value:"DML"},
			{label:"dF (01 January)",value:"DF"},
			{label:"Md (Jan 01)",value:"MD"},
			{label:"Fd (January 01)",value:"FD"},
			{label:"Md,YT (Jan 01, 2019 8:00am-5:00pm)",value:"MD,YT"},
			{label:"Full (01 January 2019 8:00am-5:00pm)",value:"full"},
			{label: "jMl (1 Jan Monday)", value: "jMl" },
            {label: "d.FY (01. January 2019)", value: "d.FY" },
            {label: "d.F (01. January)", value: "d.F" },
            {label: "ldF (Monday 01 January)", value: "ldF" },
            {label: "Mdl (Jan 01 Monday)", value: "Mdl" },
            {label: "d.Ml (01. Jan Monday)", value: "d.Ml" },
            {label: "dFT (01 January 8:00am-5:00pm)", value: "dFT" }
		 ];
		const venueOptions = [
            {label: 'NO', value: 'no'},
			{label: 'YES', value: 'yes'}
		];
		const timeOptions = [
            {label: 'Upcoming', value: 'future'},
			{label: 'Past', value: 'past'},
			{label: 'All', value: 'all'}
		];
	
		const orderOptions=[
			{label:"ASC",value:"ASC"},
			{label:"DESC",value:"DESC"}		
		];
	
	
		return [
			
			!! props.isSelected && (
				<InspectorControls key="inspector">
					<PanelBody title={ __( 'The Events Calendar Shortcode Settings' ) } >
					<SelectControl
                        label={ __( 'Events Category' ) }
                        options={ categoryList }
                        value={ props.attributes.category }
						onChange={ ( value ) =>props.setAttributes( { category: value } ) }
					/>
					<SelectControl
                        label={ __( 'Select Template' ) }
                        options={ layoutOptions }
                        value={ props.attributes.template }
						onChange={ ( value ) =>props.setAttributes( { template: value } ) }
					/>
					<SelectControl
                        label={ __( 'Template Style' ) }
                        options={ designsOptions }
                        value={ props.attributes.style }
						onChange={ ( value ) =>props.setAttributes( { style: value } ) }
					/>				
					<SelectControl
						label={ __( 'Date Formats' ) }
						description={ __( 'yes/no' ) }
						options={ dateFormatsOptions }
						value={ props.attributes.dateformat }
						onChange={ ( value ) =>props.setAttributes( { dateformat: value } ) }
					/>	
					<TextControl
						label={ __( 'Limit the events' ) }
						value={ props.attributes.limit }
						onChange={ ( value ) =>props.setAttributes( { limit: value } ) }
					/>
					<SelectControl
                        label={ __( 'Events Order' ) }
                        description={ __( ' Events Order' ) }
                        options={ orderOptions }
                        value={ props.attributes.order }
						onChange={ ( value ) =>props.setAttributes( { order: value } ) }
						/>
					<SelectControl
                        label={ __( 'Hide Venue' ) }
                        description={ __( 'Hide Venue Settings' ) }
                        options={ venueOptions }
                        value={ props.attributes.hideVenue }
						onChange={ ( value ) =>props.setAttributes( { hideVenue: value } ) }
						/>							
					<SelectControl
                        label={ __( 'Events Time (Past/Future Events)' ) }
                        description={ __( 'Events Time' ) }
                        options={ timeOptions }
                        value={ props.attributes.time }
						onChange={ ( value ) =>props.setAttributes( { time: value } ) }
					/>
					<p className="ect-note">Note:-Show events in between a date range e.g( 2017-01-01 to 2017-02-05).
					Date format - <strong>YY-MM-DD</strong></p>	
					<TextControl
						label={ __( 'Start Date | format(YY-MM-DD)' ) }
						value={ props.attributes.startDate }
						onChange={ ( value ) =>props.setAttributes( { startDate: value } ) }
					/>
					<TextControl
						label={ __( 'End Date | format(YY-MM-DD)' ) }
						value={ props.attributes.endDate }
						onChange={ ( value ) =>props.setAttributes( { endDate: value } ) }
					/>
					</PanelBody>
					<SelectControl
                        label={ __( 'Enable Social Share Buttons?' ) }
                        options={ [
							{label: 'No', value: 'no'},
							{label: 'Yes', value: 'yes'},
						] }
                        value={ props.attributes.socialshare }
						onChange={ ( value ) =>props.setAttributes( { socialshare: value } ) }
						/>		
				</InspectorControls>
			),
			<div className={ props.className }>
			<LayoutType  LayoutImgPath={LayoutImgPath} layout={props.attributes.template} />
			<div class="ect-shortcode-block">
			[events-calendar-templates
			category="{props.attributes.category}"
			template="{props.attributes.template}"
			style="{props.attributes.style}"
			date_format="{props.attributes.dateformat}"
			start_date="{props.attributes.startDate}"
			end_date="{props.attributes.endDate}"
			limit="{props.attributes.limit}"
			order="{props.attributes.order}"
			hide-venue="{props.attributes.hideVenue}"
			time="{props.attributes.time}"
			socialshare="{props.attributes.socialshare}"] <a href="https://1.envato.market/c/1258464/275988/4415?u=https%3A%2F%2Fcodecanyon.net%2Fitem%2Fthe-events-calendar-templates-and-shortcode-wordpress-plugin%2F20143286" target="_blank">Check Pro Templates</a>
			</div>
			</div>
		];
	},
	// Defining the front-end interface
	save() {
		// Rendering in PHP
		return null;
	},
});

