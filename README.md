Conditional Fields
=================

This extension works whowever it is not 100% idiot proof at the moment so you're going to have to edit the config and know the field Id's to get it to work.

1. Install the Extension

2. Add this snippet in your config file

	###### CONDITIONAL_FIELDS ######
	'conditional_fields' => array(
		'section-name' => '[
			{
				"condition": {"field-name":"value"},
				"field-id1": {"visible":"no"},
				"field-id2": {"visible":"no"},
				"field-id3": {"visible":"no"}
			}
		]',
	),
	########


3. Replace `section-name` with the section name that you would like to use section_hierarchy on

4. Replace the `field-name` with the respective field name on which you want to add your condition and `value` with the value of the field. For now this only supports literal values.

5. Replace the `field-id` with the respective field id's which you'd like to hide. For now the only supported option is `visible : 'no'` support for other features will be added on request or as need be. But things like pre-filling values and adding association filters are possible using these methods.

### Field Options

The above example shows only the first level of field options, visibility the below options are however available

1. `visible` - possible values `yes`/`no` assumes yes if not set. However if hidden by another filter will not automatically re-appear.
2. `rename` - changes the name of the field. Useful if you re-purposed a field or else want to rename it for a particular scenario
3. `key` - currently works only with a specific scenario. When using a Dynamic Text Group Field with `key` field name, this accepts an array of keys, which need to be set for the particular field. Useful if you need to add/set extra string values depending on another value. This ensures keys are added and client can add the keys without problems.
4. `filters` - works only with Association Field details below.

## Dealing with Association Filters

Association Fields now support filtering via delegates. This allows some interesting scenarios where by only a sub-set of the relative associations are visible.

	"field-id": {
		"filters":{
			"filter-field-id":"value",
			"filter-field-id2":"value2",
		}
	}

Multiple filters can be set per condition the structure is available above. The `filter-field-id` is a field id from the linked section, eg if you want to show only published articles, you would use the below example (with corrected field id's)


	###### CONDITIONAL_FIELDS ######
	'conditional_fields' => array(
		'homepage' => '[
			{
				"condition": {"field-name":"value"},
				"20": { // association field id
					"filters":{
						"3":"yes", // published yes
						"5":"earlier than now", // date is earlier than now
					}
				},
			}
		]',
	),
	########
