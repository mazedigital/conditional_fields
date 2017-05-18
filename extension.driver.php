<?php

class extension_conditional_fields extends Extension {

	
	public function getSubscribedDelegates() {
		return array(
			array(
				'page' => '/backend/',
				'delegate' => 'InitialiseAdminPageHead',
				'callback' => 'initializeAdmin',
			),
			array(
				'page' => '/publish/',
				'delegate' => 'AssociationFiltering',
				'callback' => 'associationFiltering'
			),
			array(
				'page' => '/publish/edit/',
				'delegate' => 'EntryPreRender',
				'callback' => 'entryPreRender'
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
			$page->addScriptToHead($assets_path . '/js/conditional-fields.js?' . ExtensionManager::fetchInstalledVersion('conditional_fields'), $LOAD_NUMBER++);
		}
		
	}

	/**
	 * Entry Pre Render
	 */
	public function entryPreRender($context) {

		$entry = $context['entry'];
		$section = $context['section'];
		$section_id = $section->get('id');

		$conditionalFields = Symphony::Configuration()->get($section->get('handle'),'conditional_fields');

		// var_dump($conditionalFields);die;
		$conditionalFields = json_decode($conditionalFields);

		$matchedCondition = NULL;

		if (isset($conditionalFields)){
			foreach ($conditionalFields as $rule) {
				// var_dump($rule->condition);die;
				$match = true;
				foreach ($rule->condition as $field => $value) {
					if (!isset($this->fieldID[$field])){
						$this->fieldID[$field] = FieldManager::fetchFieldIDFromElementName($field,$section_id);
					}

					$fieldData = $entry->getData($this->fieldID[$field]);
					if ( $fieldData['handle'] == $value || $fieldData['value'] == $value || $fieldData['relation_id'] == $value){
						//this is a match
					} else {
						// this is not a match
						$match = false;
					}
				}

				if ($match){
					$this->matchedCondition = get_object_vars($rule);
				}
			}
		}
	}


	/**
	 * Filter select choices - this ensures that users see only data according to the conditions set
	 */
	public function associationFiltering($context) {

		if (!isset($context['field-id']) || !isset($this->matchedCondition[$context['field-id']])){
			//stop as there are no conditions met or field set
			return;
		}

		$filters = get_object_vars($this->matchedCondition[$context['field-id']]);
		if (!isset($filters['filters'])){
			// if there are no usable filters (just a hide/show) no association filtering needs to happen
			return;
		}

		$filters =  get_object_vars($filters['filters']);

		if (isset($context['filters']) && isset($filters)){
			$context['filters'] = $filters;
		}

		//Note: the below filtering doesn't work properly if you have multiple linked sections - might need to invest more time for such scenarios
		if (!empty($filters)) {
			// Build Filters

			$field = FieldManager::fetch($context['field-id']);
			$relatedFieldID = $field->get('related_field_id');

			$relatedField = current(FieldManager::fetch($relatedFieldID));
			$relatedSectionID = $relatedField->get('parent_section');

			$joins = "";
			$where = "";

			foreach ($filters as $handle => $value) {
				if (!is_array($value)) {
					$filter_type = Datasource::determineFilterType($value);
					$value = preg_split('/'.($filter_type == Datasource::FILTER_AND ? '\+' : '(?<!\\\\),').'\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
					$value = array_map('trim', $value);
					$value = array_map(array('Datasource', 'removeEscapedCommas'), $value);
				}

				$handle = Symphony::Database()->cleanValue($handle);
				$filter_id = FieldManager::fetchFieldIDFromElementName($handle,$relatedSectionID);

				$field = FieldManager::fetch($filter_id);
				if ($field instanceof Field) {
					$field->buildDSRetrievalSQL($value, $joins, $where, ($filter_type == Datasource::FILTER_AND ? true : false));
				}
			}

			$context['where'] .= $where;
			$context['joins'] .= $joins;

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