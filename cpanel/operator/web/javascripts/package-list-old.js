$(document).ready(function(){
	
	/**
	 * Based on identical feature set, toggle a package's CDN feature.
	 * 
	 * @author yclian
	 * @since 2.5
	 * @version 2.5.20100612
	 */
	$('select[id^=package_cdn_status]').change(function(){
		
		var regex_package_name = new RegExp('^package_cdn_statuses\\[(.+)\\]');
		var origin = this;
		// Get the feature name of the changed package
		var package_name = regex_package_name.exec(this.id)[1];
		var feature_name = $('#packages\\['+package_name+'\\]\\[feature_set\\]').attr('value');
	
		// Loop through all packages, and check if they're on identical feature
		// name. If so, we make the same change.
		$('select[id^=package_cdn_status]').each(function(i){
			
			// regex pull their package name out. get their feature again too.
			// if same, we toggle.
			var package_name2 = regex_package_name.exec(this.id)[1];
			var feature_name2 = $('#packages\\['+package_name2+'\\]\\[feature_set\\]').attr('value');
			
			if(package_name == package_name2){
				// Proceed to next one as it's the same package.
				return;
			}
			
			if(feature_name == feature_name2){

				var target_id = '#package_cdn_statuses\\[' + package_name2 + '\\]';

				if($(origin).val() == 1){
					$(target_id + ' option[value=1]').attr('selected', 'selected');
				} else{
					$(target_id + ' option[value=0]').attr('selected', 'selected');
				}
				
				// UI effect
				$(target_id).parent().parent().parent().effect("highlight", { 'color': '#ffb1a4' }, 3000);
			}
		});
		
	});

});
