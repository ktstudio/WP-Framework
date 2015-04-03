jQuery(document).ready(function () {

    // Validování metaboxu v editaci postu (custom post_type)
    jQuery("form#post, form#kt-custom-page-screen").submit(function () {
        jQuery("#jquery-kt-validator").remove();

        var validationResult = jQuery(this).formValidation();
        var formNotice = "<div id=\"jquery-kt-validator\" class=\"error\">" +
                "<p>Ve formuláři se vyskytla chyba. Zkontrolujte data a proces opakujte.</p>" +
                "</div>";

        if (validationResult === false) {
            jQuery("div.wrap h2").after(formNotice);
        }

        return validationResult;
    });

    // Validování formuláře pomocí jQuery globálně na základě data attributu, který vpisován defaulntě KT_Form třídou
    jQuery("[data-validate=\"jquery\"]").submit(function () {
        jQuery("#jquery-kt-validator").remove();

        var validationResult = jQuery(this).formValidation();
        var formNotice = "<div id=\"jquery-kt-validator\" class=\"error\">" +
                "<p> Ve formuláři se vyskytla chyba.</p>" +
                "</div>";

        if (validationResult === false) {
            jQuery(this).before(formNotice);
        }

        return validationResult;
    });


    // Po editaci inputu dojde k zrušení error msg
    jQuery('table.kt-form-table input').blur(function () {
        jQuery(this).next('div').find('span.erorr-s').delay(500).fadeOut(400);
    });

    // Inicializace switchFieldu
    jQuery('.switch').each(function () {
        var input = jQuery(this).children('input[type=hidden]');
        var toggle = jQuery(this).children('span.switch-toggle');

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
    jQuery('body').on("click", ".switch-toggle", function () {
        var element = jQuery(this);
        var input = element.next('input[type=hidden]');
        var toggle = element;
        switchToggle(input, toggle);
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
    jQuery("table.item-list span.delete-row").click(function () {
        if (confirm('Chcete opravdu trvale smazat tento záznam?')) {
            var rowId = jQuery(this).attr("data-id");
            var deletingRow = jQuery("table.item-list tr#row-" + rowId);

            deletingRow.css('background-color', 'red');

            data = {
                action: "kt_delete_row_from_table_list",
                type: jQuery(this).attr("data-type"),
                rowId: rowId
            };

            jQuery.post(ajaxurl, data, function (response) {
                deletingRow.fadeOut(500, "", function () {
                    jQuery(this).remove();
                });
            });
        }
    });

    // Ajax událost pro editaci switchFieldu v rámci KT_CRUD_List
    jQuery("body").on("click", ".edit-crud-switch-list-field", function () {
        var input = jQuery("#" + jQuery(this).attr("for"));

        data = {
            action: "kt_edit_crud_list_switch_field",
            type: input.data("item-type"),
            rowId: input.data("item-id"),
            columnName: input.data("column-name"),
            value: input.val()
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response === 1) {
                switchToggle(input, jQuery(this));
            }
        });

    });

    // Obsluha a vyvolání WP Gallery pop up okna pro výběr obrázku
    jQuery('body').on("click", ".kt-file-loader", function (e) {
        var kt_input_id = jQuery(this).attr('id');
        var button = jQuery(this);
        var deleteButtonContent = '<a class="remove-file"><span class="dashicons dashicons-no"></span></a>';
        var fileContent = "";

        wp.media.editor.send.attachment = function (props, attachment) {

            if (attachment.type === "image") {
                fileContent = '<img class=\"file\" src="' + attachment.sizes.thumbnail.url + '">';
            } else {
                fileContent = '<span class=\"file\">' + attachment.title + '</span>';
            }

            jQuery("." + kt_input_id).html(fileContent + deleteButtonContent);
            jQuery("#" + kt_input_id).val(attachment.id);
        };

        wp.media.editor.open(button);

        return false;
    });

    // Obsluha smazání obrázku a vyčištění inputu pro uložení
    jQuery('body').on("click", ".remove-file", function (e) {
        var removeButton = jQuery(this);
        jQuery(this).prev(".file").fadeOut(300, function () {
            jQuery(this).remove();
            removeButton.remove();
        });
        jQuery(this).parents(".file-load-box").find("input").val("");
    });

    // Přepínání switch toggle buttonu na základě inputu a a toggle
    function switchToggle(input, toggle) {
        if (input.val() == '1') {
            toggle.addClass('off');
            toggle.removeClass('on');
            input.attr('value', '0');
        } else {
            toggle.addClass('on');
            toggle.removeClass('off');
            input.attr('value', '1');
        }
    }

    // Sortable číselníku vycházející z KT_Catalog_Base_Modelu
    var sortableTablefixHelper = function (e, ui) {
        ui.children().each(function () {
            jQuery(this).width(jQuery(this).width());
        });
        return ui;
    };

    var sortableTableSave = function (e, ui) {
        var sortingBody = jQuery("table[data-sortable='true'] tbody");
        var sortedItems = {};
        var className = sortingBody.parent("table").data("class-name");

        sortingBody.find("tr").each(function (i) {
            sortedItems[i] = jQuery(this).data("item-id");
        });

        data = {
            action: "kt_edit_sorting_crud_list",
            data: sortedItems,
            class_name: className
        };

        jQuery.post(ajaxurl, data);
    };

    jQuery("table[data-sortable='true'] tbody").sortable({
        helper: sortableTablefixHelper,
        stop: sortableTableSave
    }).disableSelection();

    // Slider input - jQuery UI
    
    jQuery(".sliderInputElement").each(function () {
        var slider = jQuery(this);
        var input = slider.find("input");
        var min = slider.data("min");
        var max = slider.data("max");
        var step = slider.data("step");
        var value = min;
        
        if(input.val() != min){
            value = input.val();
        }
        
        jQuery(this).find("input").addClass("hidden");

        jQuery(this).slider({
            range: false,
            step: step,
            min: min,
            max: max,
            value: value,
            slide: function (event, ui) {
                var parent = slider.parent("div.sliderContainer");
                parent.find(".ui-slider-handle").text(ui.value);
                slider.find("input.inputMin").val(ui.value);
            },
            create: function( event, ui ) {
                jQuery(this).find(".ui-slider-handle").text(value);
            }
        });
    });
    

});