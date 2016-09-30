//console.log("Welcome in dynamic madness");
jQuery(document).ready(function () {

    if (typeof kt_urls === "undefined") {
        console.log("kt_urls not set dynamic forms wont work !!!");
        return;
    }
    if (jQuery(".fieldset-field").length < 1) {
        return;
    }
    jQuery("body").on('click', '.fieldset-field .kt-add-fieldset', function () {
        var parentDom = jQuery(this).parent();
        var counterDom = parentDom.find(".ff_count");
        var data = {
            action: "kt_generate_fieldset",
            config: parentDom.attr("data-config"),
            fieldset: parentDom.attr("data-fieldset"),
            number: parseInt(counterDom.val()) + 1
        };
        jQuery.get(kt_urls.ajaxurl, data, function (data) {
            parentDom.find(".sets").append(data);
            counterDom.val(parseInt(counterDom.val()) + 1);
            kt_dynamic_fields_on_add();
        });
    });
    jQuery("body").on('click', '.fieldset-field .kt-remove-fieldset', function () {
        jQuery(this).parent().parent().remove();
    });   

    kt_dynamic_fieldset_setup();
});

function kt_dynamic_fields_on_add() {
    kt_core_setup_forms_fields();
    kt_dynamic_fieldset_setup();
}

function kt_dynamic_fieldset_setup() {
    jQuery(".fieldset-field .sets").sortable({
        update: function (event, ui) {
            var parentDom = ui.item.parent().parent().parent();
            var namePrefix = parentDom.attr("data-fieldset");
            var reg = new RegExp(namePrefix + "\\-\\d+", "i");
            parentDom.find(".set").each(function (index, _) {
                var inputDoms = jQuery(this).find("[name|='" + namePrefix + "']");           
                inputDoms.each(function () {
                    var oldName = jQuery(this).attr("name");
                    var newName = oldName.replace(reg, namePrefix + "-" + index);
                    jQuery(this).attr("name", newName);
                });
            });
        }
    });
}

