Conditional Fields
=================

This extension works whowever it is not 100% idiot proof at the moment so you're going to have to edit the config and know the field Id's to get it to work.

1. Install the Extension

2. Add this snippet in your config file

	###### CONDITIONAL_FIELDS ######
	'conditional_fields' => array(
		'section-name' => '[
			{
				\'condition\': {\'field-name\':\'value\'},
				\'field-id\': {\'visible\':\'no\'},
				\'field-id\': {\'visible\':\'no\'},
				\'field-id\': {\'visible\':\'no\'}
			}
		]',
	),
	########


3. Replace `section-name` with the section name that you would like to use section_hierarchy on

4. Replace the `field-name` with the respective field name on which you want to add your condition and `value` with the value of the field. For now this only supports literal values.

5. Replace the `field-id` with the respective field id's which you'd like to hide. For now the only supported option is `visible : 'no'` support for other features will be added on request or as need be. But things like pre-filling values and adding association filters are possible using these methods.