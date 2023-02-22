import PreviewElementHandler from '../bases/preview-element-handler';

export default class FontsHandler extends PreviewElementHandler {

	constructor( editorHelper, config ) {
		super( editorHelper, config );
	}

	/**
	 * inject custom fonts
	 * @param rootContainer {Container} - root container
	 * @param customFonts {Object[]} - custom fonts { _id, title}
	 */
	injectCustomElements( rootContainer, customFonts ) {
		const sections = this.helper.findElementsByClass( rootContainer, this.getSelector( 'font_section' ) );

		if ( 0 === sections.length ) {
			throw new Error( 'No fonts sections found' );
		}

		const lastFontSection = sections[ sections.length - 1 ];
		const defaultContainer = this.getDefaultContainer( rootContainer );

		if ( ! defaultContainer ) {
			throw new Error( 'No default font container found' );
		}

		let injectionPoint = this.injectCustomTitle( rootContainer, lastFontSection );
		for ( const font of customFonts ) {
			injectionPoint = this.addCustomFont( rootContainer, injectionPoint, defaultContainer, font );
		}
	}

	/**
	 * add custom font
	 * @param rootContainer {Container} - root container
	 * @param injectionContainer {Container} - injection container to inject after
	 * @param defaultContainer {Container} - default container to clone
	 * @param font {Object} - font { _id, title }
	 * @return {Container} - injected container
	 */
	addCustomFont( rootContainer, injectionContainer, defaultContainer, font ) {
		let injected = this.helper.injectAfter( injectionContainer, defaultContainer.model.toJSON() );

		if ( ! injected ) {
			throw new Error( 'Failed to inject custom font' );
		}

		this.setSectionValues( injected, font );

		return injected;
	}

	/**
	 * set font section values
	 * @param container {Container} - container to set values in
	 * @param font {Object} - font { _id, title }
	 */
	setSectionValues( container, font ) {
		const { _id: id, title } = font;

		// Get the title widgets by class - should be only one
		const titleWidgets = this.helper.findElementsByClass( container, this.getSelector( 'font_title_widget' ) );
		// Get the font widgets by class - should be only one
		const fontWidgets = this.helper.findElementsByClass( container, this.getSelector( 'font_widget' ) );

		let titleWidget = titleWidgets[ 0 ];
		let fontWidget = fontWidgets[ 0 ];

		if ( ! titleWidget || ! fontWidget ) {
			throw new Error( 'No title or widget found' );
		}

		this.setFontGlobal( fontWidget, id );

		this.setFontTitle( titleWidget, title );

		this.helper.setElementSettings(
			fontWidget,
			{
				_element_id: this.getSelector( 'font_widget' ) + '__' + font._id,
				header_size: 'p',
			}
		);

	}

	/**
	 * set font title
	 * @param titleWidget
	 * @param title
	 */
	setFontTitle( titleWidget, title ) {
		this.helper.setElementSettings(
			titleWidget,
			{
				title: title,
			}
		);
	}

	/**
	 * set font global
	 * @param fontWidget
	 * @param id
	 */
	setFontGlobal( fontWidget, id ) {
		this.helper.setGlobalValues(
			fontWidget,
			{
				typography_typography: "globals/typography?id=" + id,
			},
		);
	}

	/**
	 * inject custom title
	 * @param rootContainer {Container} - root container
	 * @param injectionContainer {Container} - injection container to inject after
	 * @return {Container}
	 */
	injectCustomTitle( rootContainer, injectionContainer ) {
		const defaultTitleContainer = this.getDefaultTitleContainer( rootContainer );

		if ( ! defaultTitleContainer ) {
			throw new Error( 'No default title container found' );
		}

		const newTitle = this.helper.injectAfter( injectionContainer, defaultTitleContainer.model.toJSON() );

		if ( ! newTitle ) {
			throw new Error( 'Could not inject custom title' );
		}

		this.helper.setElementSettings( newTitle, {
			title: 'Custom Fonts',
			_element_id: this.getSelector( 'custom_fonts_title' ),
		} );

		return newTitle;
	}

	/**
	 * get default title container
	 * @param rootContainer {Container} - root container
	 * @return {Container|null}
	 */
	getDefaultTitleContainer( rootContainer ) {
		return this.helper.findElementById( rootContainer, this.getSelector( 'default_title_container' ) );
	}

	/**
	 * get default container
	 * @param rootContainer {Container} - root container
	 * @return {Container|null}
	 */
	getDefaultContainer( rootContainer ) {
		return this.helper.findElementById( rootContainer, this.getSelector( 'default_fonts_section' ) );
	}
}
