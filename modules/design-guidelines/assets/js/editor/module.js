import EditorHelper from "./utils/editorHelper";
import ColorsComponent from './colors/component';
import FontsComponent from './fonts/component';

class Module extends elementorModules.editor.utils.Module {
	onInit() {
		const config = window[ 'elementorDesignGuidelinesConfig' ];

		if ( ! config ) {
			return;
		}
		const helper = new EditorHelper();
		$e.components.register( new ColorsComponent(helper, config) );
		$e.components.register( new FontsComponent(helper, config) );

		this.colorListener();
	}

	colorListener() {
		window.addEventListener( 'elementor/global-color/show', ( e ) => {
			console.log( e );
			console.log( e.detail );
		} );
	}
}

new Module();

