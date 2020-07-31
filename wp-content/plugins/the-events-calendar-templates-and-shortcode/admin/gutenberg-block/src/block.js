/**
 * Block dependencies
 */

import classnames from 'classnames';
import EctIcon from './icons';
import LayoutType from './layout-type';
const baseURL=ectUrl;
const LayoutImgPath=baseURL+'admin/gutenberg-block/layout-images/';
/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { apiFetch } = wp;
const {
	RichText,
	InspectorControls,
	BlockControls,
} = wp.editor;
const {
	registerStore,
	withSelect,
} = wp.data;

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

const catgorySelections = [];
//http://localhost/wp-test/wp-json/tribe/events/v1/categories

let categoryList = [];
let tagList=[];
let venueList=[];
let organizerList=[];


wp.apiFetch({path:'/tribe/events/v1/categories/?per_page=50'}).then(data => {
 if(typeof(data.categories)!=undefined){
	categoryList=data.categories.map(function(val,key){
		return {label: val.name, value: val.slug};
	});
	}
	categoryList.push({label: "Select a Category", value:'all'});
});


wp.apiFetch({path:'/tribe/events/v1/tags/?per_page=50'}).then(data => {
	if(typeof(data.tags)!=undefined){
		tagList=data.tags.map(function(val,key ) {
 		 return {label: val.name, value: val.slug};
  		 });
	}
	tagList.push({label: "Select a Tag", value:''}); 
});


wp.apiFetch({path:'/tribe/events/v1/venues/?per_page=50&status=publish'}).then(data => {
	if(data.venues!=undefined){
		venueList=data.venues.map(function(val,key) {
  		 return {label: val.venue, value: val.id};
  		 }); 
	}
	venueList.push({label: "Select a Venue", value:''});
   });
 

wp.apiFetch({path:'/tribe/events/v1/organizers/?per_page=50&status=publish'}).then(data => {
	if(data.organizers!=undefined){
		organizerList=data.organizers.map(function( val,key) {
	   return {label: val.organizer, value: val.id};
  		 });
	} 
	organizerList.push({label: "Select a Organizer", value:''});
   });
  



/**
 * Register block
 */
export default registerBlockType( 'ect/shortcode', {
		// Block Title
		title: __( 'Events Calendar Shortcode' ),
		// Block Description
		description: __( 'The Events Calendar - Shortcode & Templates Pro Addon' ),
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
		featuredonly: {
			type: 'string',
			default:  'false',
		},
		columns: {
			type: 'string',
			default:2,
		},
		autoplay: {
			type: 'string',
			default:  'true',
		},
		tags: {
			type: 'string',
			default: ''
		},
		venues: {
			type: 'string',
			default: ''
		},
		organizers: {
			type: 'string',
			default: ''
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
			{label: 'Slider Layout', value: 'slider-view'},
			{label:'Carousel Layout',value:'carousel-view'},
			{label:'Grid Layout',value:'grid-view'},
			{label:'Masonry Layout(Categories Filters)',value:'masonry-view'},
			{label:'Toggle List Layout',value:'accordion-view'}
		];
		const colContains=[
			"carousel-view",
			"grid-view",
			"masonry-view"
		];

	
		const dateFormatsOptions = [
			{label:"Default (01 January 2019)",value:"default"},
			{label:"Md,Y (Jan 01, 2019)",value:"MD,Y"},
			{label:"Fd,Y (January 01, 2019)",value:"MD,Y"},
			{label:"dM (01 Jan)",value:"DM"},
			{label:"dF (01 January)",value:"DF"},
			{label:"Md (Jan 01)",value:"MD"},
			{label:"Fd (January 01)",value:"FD"},
			{label:"Md,YT (Jan 01, 2019 8:00am-5:00pm)",value:"MD,YT"},
			{label:"Full (01 January 2019 8:00am-5:00pm)",value:"full"},
			{label:"jMl (1 Jan Monday)",value:"jMl"},
			{label:"d.FY (01. January 2019)",value:"d.FY"},
			{label:"d.F (01. January)",value:"d.F"},
			{label:"d.Ml (01. Jan Monday)",value:"d.Ml"},
			{label:"ldF (Monday 01 January)",value:"ldF"},
			{label:"Mdl (Jan 01 Monday)",value:"Mdl"},
			{label:"dFT (01 January 8:00am-5:00pm)",value:"dFT"},
	//		{label:"Custom(Using The Events Calendar settings)",value:"custom"},
		 ];
		const designsOptions = [
			{label: 'Style 1', value: 'style-1'},
			{label: 'Style 2', value: 'style-2'},
			{label: 'Style 3', value: 'style-3'},
		];
		const skinOptions=[
			{label:"default",value:"default"},
			{label:"light",value:"light"},
			{label:"dark",value:"dark"}
	   ];
		
		const venueOptions = [
            {label: 'NO', value: 'no'},
			{label: 'YES', value: 'yes'},
		];
		const timeOptions = [
            {label: 'Future', value: 'future'},
			{label: 'Past', value: 'past'},
			{label: 'All', value: 'all'},
		];
		const autoPlayOptions=[
			{label: 'True', value: 'true'},
			{label: 'False', value: 'false'},
			];
	   
		const orderOptions=[
			{label:"DESC",value:"DESC"},
			{label:"ASC",value:"ASC"}
		];
		const columnsOptions=[
			{label:'2',value:'2'},
			{label:'3',value:'3'},
			{label:'4',value:'4'},
			{label:'6',value:'6'},
		];

		return [
			
			!! props.isSelected && (
				<InspectorControls key="inspector">
					<PanelBody title={ __( 'Basic Settings' ) } >
					<SelectControl
                        label={ __( 'Select Category' ) }
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
                        label={ __( 'Select Style' ) }
                        description={ __( 'Select Style' ) }
                        options={ designsOptions }
                        value={ props.attributes.style }
						onChange={ ( value ) =>props.setAttributes( { style: value } ) }
						/>
				{colContains.includes(props.attributes.template)&&
					<SelectControl
                        label={ __( 'Columns' ) }
                        description={ __( 'Columns' ) }
                        options={ columnsOptions }
                        value={ props.attributes.columns }
						onChange={ ( value ) =>props.setAttributes( { columns: value } ) }
						/>
				}
			
				{ props.attributes.template=="slider-view" &&
					<SelectControl
					label={ __( 'AutoPlay' ) }
					description={ __( 'AutoPlay' ) }
					options={ autoPlayOptions }
					value={ props.attributes.autoplay }
					onChange={ ( value ) =>props.setAttributes( { autoplay: value } ) }
				/>					
				}
				 {props.attributes.template=="carousel-view" &&		
						<SelectControl
                        label={ __( 'AutoPlay' ) }
                        description={ __( 'AutoPlay' ) }
                        options={ autoPlayOptions }
                        value={ props.attributes.autoplay }
						onChange={ ( value ) =>props.setAttributes( { autoplay: value } ) }
					/>					
				}		
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
				
						</PanelBody>
						<PanelBody title={ __( 'Extra Settings' ) } >	
						<SelectControl
                        label={ __( 'Hide Venue' ) }
                        description={ __( 'Hide Venue Settings' ) }
                        options={ venueOptions }
                        value={ props.attributes.hideVenue }
						onChange={ ( value ) =>props.setAttributes( { hideVenue: value } ) }
						/>	
					<SelectControl
                        label={ __( 'Show Only Featured Events' ) }
                        options={ [
							{label: 'No', value: 'false'},
							{label: 'Yes', value: 'true'},
						] }
                        value={ props.attributes.featuredonly }
						onChange={ ( value ) =>props.setAttributes( { featuredonly: value } ) }
					/>		
					<SelectControl
                        label={ __( 'Events Time' ) }
                        description={ __( 'Events Time' ) }
                        options={ timeOptions }
                        value={ props.attributes.time }
						onChange={ ( value ) =>props.setAttributes( { time: value } ) }
					/>	
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
						<p className="description">Note:-Show events from date range e.g( 2017-01-01 to 2017-02-05).
						Please dates in this format(YY-MM-DD)</p>
						<SelectControl
                        label={ __( 'Select Tags' ) }
                        options={ tagList }
                        value={ props.attributes.tags }
						onChange={ ( value ) =>props.setAttributes( { tags: value } ) }
						/>

						<SelectControl
                        label={ __( 'Select Venue' ) }
                        options={ venueList }
                        value={ props.attributes.venues }
						onChange={ ( value ) =>props.setAttributes( { venues: value } ) }
						/>
						<SelectControl
                        label={ __( 'Select Organizer' ) }
                        options={ organizerList }
                        value={ props.attributes.organizers }
						onChange={ ( value ) =>props.setAttributes( { organizers: value } ) }
						/>
						<SelectControl
                        label={ __( 'Enable Social Share Buttons?' ) }
                        options={ [
							{label: 'No', value: 'no'},
							{label: 'Yes', value: 'yes'},
						] }
                        value={ props.attributes.socialshare }
						onChange={ ( value ) =>props.setAttributes( { socialshare: value } ) }
						/>		
					</PanelBody>
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
			featured-only="{props.attributes.featuredonly}" 
			columns="{props.attributes.columns}" 
			autoplay="{props.attributes.autoplay}"
			tags="{props.attributes.tags}"
			venues="{props.attributes.venues}"
			organizers="{props.attributes.organizers}"
			socialshare="{props.attributes.socialshare}"
			]	  
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

