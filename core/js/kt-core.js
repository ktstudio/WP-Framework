jQuery(document).ready(function() {
    
    // Validování metaboxu v editaci postu (custom post_type)
    jQuery("form#post").submit(function(){

        jQuery("#jquery-kt-validator").remove();

        var validationResult = jQuery(this).formValidation();
        var formNotice = "<div id=\"jquery-kt-validator\" class=\"error\">" +
                    "<p>Ve formuláři se vyskytla chyba. Zkontrolujte data a proces opakujte.</p>" +
                    "</div>";

        if(validationResult === false){
            jQuery("div.wrap h2").after(formNotice);
        }

        return validationResult;
    });
    
    // Validování formuláře pomocí jQuery globálně na základě data attributu, který vpisován defaulntě KT_Form třídou
    jQuery("[data-validate=\"jquery\"]").submit(function(){
        jQuery("#jquery-kt-validator").remove();
        
        var validationResult = jQuery(this).formValidation();
        var formNotice = "<div id=\"jquery-kt-validator\" class=\"error\">" +
            "<p> Ve formuláři se vyskytla chyba.</p>" +
            "</div>";
        
        if(validationResult === false){
            jQuery(this).before(formNotice);
        }
        
        return validationResult;
    });
    

    // Po editaci inputu dojde k zrušení error msg
    jQuery('table.kt-form-table input').blur(function() {
            jQuery(this).next('div').find('span.erorr-s').delay(500).fadeOut(400);
    });

    // Inicializace switchFieldu
    jQuery('.switch').each(function() {
            var input = jQuery(this).children('input[type=hidden]');
            var toggle = jQuery(this).children('label.switch-toggle');

            if (input.length) {
                    input.addClass('hidden');
                    toggle.removeClass('hidden');
                    if (input.val() == '1') {
                            toggle.addClass('on');
                            toggle.removeClass('off');
                    } else {
                            toggle.addClass('off');
                            toggle.removeClass('on');
                    }
                    ;
            }
    });

    // Aktivace tooltip pro inputy KT_Field
    jQuery('.kt-field').tooltip();

    // Přepínání switch fieldu
    jQuery('body').on("click", ".switch-toggle", function() {
        var element = jQuery(this);
        var input = element.next('input[type=hidden]');
        var toggle = element;

        if (input.val() == '1') {
            toggle.addClass('off');
            toggle.removeClass('on');
            input.attr('value', '0');
        } else {
            toggle.addClass('on');
            toggle.removeClass('off');
            input.attr('value', '1');
        }
    });
    
    // Počeštění jQuery data pickeru
    jQuery(".datapicker").datepicker({
            dateFormat: "dd.mm.yy",
            dayNames: ["Neděle", "Pondělí", "Úterý", "Sřteda", "Čtvrtek", "Pátek", "Sobota"],
            dayNamesMin: ["Ne", "Po", "Út", "St", "Čt", "Pá", "So"],
            monthNames: ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"],
            monthNamesShort: ["Led", "Úno", "Bře", "Dub", "Kvě", "Čer", "Červ", "Srp", "Zář", "Říj", "Lis", "Pro"],
            nextText: "Další",
            prevText: "Předchozí"
    });

    // Ajax událost pro smazání záznamu z Item tables
    jQuery("table.item-list span.delete-row").click(function(){
        if (confirm('Chcete opravdu trvale smazat tento záznam?')) {
            var rowId = jQuery(this).attr("data-id");
            var deletingRow = jQuery("table.item-list tr#row-" + rowId);

            deletingRow.css('background-color', 'red');

            data = {
                action: "kt_delete_row_from_table_list",
                type: jQuery(this).attr("data-type"),
                rowId: rowId
            };

            jQuery.post(ajaxurl, data, function(response) {
                deletingRow.fadeOut(500, "", function(){
                    jQuery(this).remove();
                });
            });
        }
    });
    
    // Obsluha a vyvolání WP Gallery pop up okna pro výběr obrázku
    jQuery('body').on("click", ".kt-file-loader", function(e) {
        var kt_input_id = jQuery(this).attr('id');
        var button = jQuery(this);
        var deleteButtonContent = '<a class="remove-file"><span class="dashicons dashicons-no"></span></a>';
        var fileContent = "";
        
        wp.media.editor.send.attachment = function(props, attachment){
            
           if(attachment.type === "image"){
                fileContent = '<img class=\"file\" src="' + attachment.sizes.thumbnail.url + '">';
            } else {
                fileContent = '<span class=\"file\">'+ attachment.title +'</span>';
            }
            
            jQuery("." + kt_input_id).html( fileContent + deleteButtonContent);
            jQuery("#" + kt_input_id).val(attachment.id);
        };
        
        wp.media.editor.open(button);

        return false;
    });
    
    // Obsluha smazání obrázku a vyčištění inputu pro uložení
    jQuery('body').on("click", ".remove-file", function(e) {
        var removeButton = jQuery(this);
        jQuery(this).prev(".file").fadeOut(300, function(){
            jQuery(this).remove();
            removeButton.remove();
        });
        jQuery(this).parents(".file-load-box").find("input").val("");
    });
});