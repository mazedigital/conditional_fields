jQuery(function($) {

	function processCondition(event){
		$this = $(this);

		//reset all filters to start with
		$('.field').show();

		if ($this.val() == event.data.value){
			event.stopImmediatePropagation();

			//check if there are multiple conditions if so verify all of them meet the criteria
			// console.log('condition triggered');

			//process each field
			for(var field in event.data.options) {
				if(event.data.options.hasOwnProperty(field)){
					if (field == 'condition') continue;

					fieldOptions = event.data.options[field];

					if (fieldOptions.visible && fieldOptions.visible == 'no'){
						$('#field-' + field).hide();
					} else {
						$('#field-' + field).show();						
					}
				}
			}
		}
	}

	$(document).on('ready.conditional-fields', function() {
		try {

			if (typeof(Symphony.ConditionalFields) == 'undefined')
				return;

			//there are conditional fields

			//register for on change events for each field which is conditional
			$(Symphony.ConditionalFields).each(function(index,object){
				for(var field in object.condition) {
					if(object.condition.hasOwnProperty(field)){
						$(document).on('change.conditional-fields','*[name="fields['+field+']"],*[name="fields['+field+'][]"]',{'value':object.condition[field],'options':object},processCondition);

						//in case this is already a set value trigger a change event to check
						$('*[name="fields['+field+']"],*[name="fields['+field+'][]"]').trigger('change.conditional-fields'); 
					}
				}
			});

		} catch (e) {}
	});

},(jQuery));