console.log("Welcome in dynamic madness");
jQuery( document ).ready(function() {
  if(typeof ajaxurl === undefined){
      console.log("Unknown ajaxurl dynamic forms wont work !!!");
      return;
  }
  jQuery("body").on('click', '.kt-add-fieldset',function(){
      var parentDom = jQuery(this).parent();
      console.log(parentDom);
      var counterDom = parentDom.find(".ff_count");
      var data = {
          action: "kt_generate_fieldset",
          config: parentDom.attr("data-config"),
          fieldset: parentDom.attr("data-fieldset"),
          number: parseInt(counterDom.val()) + 1
      };
      jQuery.get(ajaxurl,data,function(data){
          parentDom.find(".sets").append(data);
          counterDom.val(parseInt(counterDom.val()) + 1);
          kt_core_setup_forms_fields();
      });            
  });
  jQuery("body").on('click','.kt-remove-fieldset',function(){
      jQuery(this).parent().parent().remove();
  });
});

