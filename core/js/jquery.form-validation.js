( function ( $ ) {

    /**
     * jQuery plugin pro validaci KT_Form na straně browseru
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @returns {Boolean}
     */

    $.fn.formValidation = function () {

        var dataValidators = "validators";
        var isValid = true;
        var methods = {
            validate: function ( element ) {
                var validators = element.data( dataValidators );

                for ( var index in validators ) {

                    var currentValidator = validators[index];
                    var validatorFunction = currentValidator.condition;

                    if ( currentValidator.condition !== "required" && element.val() === "" ) {
                        continue;
                    }

                    var result = methods[validatorFunction]( element.val(), currentValidator.params );

                    if ( result === false ) {
                        element.after( methods.errorMsgContent( currentValidator.msg ) );
                        isValid = false;
                        return;
                    }
                }
            },
            // validační funkce na základě předané hodnoty

            required: function ( value, param ) {
                if ( value !== "" ) {
                    return true;
                }

                return false;
            },
            integer: function ( value, param ) {
                return value % 1 === 0;
            },
            float: function ( value, param ) {
                 var RE = /^-{0,1}\d*\.{0,1}\d+$/;
                return (RE.test(value));
            },
            email: function ( value, param ) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return regex.test( value );
            },
            url: function ( value ) {
                var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
                return regexp.test( value );
            },
            range: function ( value, param ) {
                
                console.log(!methods.float( value ));

                if ( !methods.float( value ) ) {
                    return false;
                }

                var minValue = parseInt(param[0]);
                var maxValue = parseInt(param[1]);

                if ( value >= minValue && value <= maxValue ) {
                    return true;
                }

                return false;
            },
            length: function ( value, param ) {
                if ( value.length === param ) {
                    return true;
                }

                return false;
            },
            maxLength: function ( value, param ) {
                if ( value.length <= param ) {
                    return true;
                }

                return false;
            },
            minLength: function ( value, param ) {
                if ( value.length >= param ) {
                    return true;
                }

                return false;
            },
            maxNumber: function ( value, param ) {
                if ( !methods.float( value ) ) {
                    return false;
                }
                
                value = parseInt(value);

                if ( value <= param ) {
                    return true;
                }

                return false;
            },
            minNumber: function ( value, param ) {
                if ( ! methods.float( value ) ) {
                    return false;
                }
                
                value = parseInt(value);
                

                if ( value >= param ) {
                    return true;
                }

                return false;
            },
            regular: function(value, param){
                var patt = new RegExp(param);
                return patt.test(value);
            },
            // funkce vrátím HTML s chybovou hláškou na základě předané MSG
            errorMsgContent: function ( msg ) {
                var html = "<div class=\"validator\">" +
                    "<span class=\"erorr-s\">" + msg + "</span>" +
                    "</div>";

                return html;
            }
        };

        $( this ).find( "[data-" + dataValidators + "]" ).each( function () {
            methods.validate( $( this ) );
        } );

        return isValid;

    };
} )( jQuery );