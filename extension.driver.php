<?php

class extension_conditional_fields extends Extension {

	
	public function getSubscribedDelegates() {
		return array(
			array(
				'page' => '/backend/',
				'delegate' => 'InitialiseAdminPageHead',
				'callback' => 'initializeAdmin',
			),
		);
	}
	
	/**
	 * Some admin customisations
	 */
	public function initializeAdmin($context) {
		$LOAD_NUMBER = 935935211;

		$page = Administration::instance()->Page;
		$assets_path = URL . '/extensions/conditional_fields/assets';

		$conditionalFields = Symphony::Configuration()->get('conditional_fields');
		$sections = array_keys($conditionalFields);
				
		// Only load on /publish/static-pages/ [this should be a variable]
		if ( in_array($page->_context['section_handle'] , $sections) && ($page->_context['page'] == 'edit' || $page->_context['page'] == 'new')) {

			Administration::instance()->Page->addElementToHead(
				new XMLElement('script', 'Symphony.ConditionalFields='.$conditionalFields[$page->_context['section_handle']], array(
					'type' => 'text/javascript'
				))
			);

			// $page->addStylesheetToHead($assets_path . '/admin.css', 'all', $LOAD_NUMBER++);
			$page->addScriptToHead($assets_path . '/js/conditional-fields.js', $LOAD_NUMBER++);
		}
		
	}
		
	//todo add preferences page to add the settings for now use the below for a guide on what needs to be added within the config

	/*
		###### conditional_fields ######
		'conditional_fields' => array(
			'sections' => array(
					'condition' => array(
							'type' => 'Article Section'
						),
					'14' => array(
							'visible' => 'no',
							'value' => '15',
							'filters' => '15',
						)
				),
			'pages' => array(
					'title' => '74',
					'parent' => '77'
				)
		),
		########
	*/
  
}  
?>