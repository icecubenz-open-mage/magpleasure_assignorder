/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Common
 * @version    master
 * @copyright  Copyright (c) 2012-2015 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */


(function( $ ) {
    $.widget( "ui.ajaxcombobox", {
        _create: function() {
            var input,
                self = this,
                cache = [],
                options = self.options,
                hidden = this.element.hide(),
                value = hidden.val() ? hidden.text() : "",
                wrapper = this.wrapper = $( "<span>" )
                    .addClass( "ui-combobox" )
                    .insertAfter( hidden );

            var url_pattern = options.url_pattern;
            var limit = options.limit;
            var page = 1;


            input = $( "<input>" )
                .appendTo( wrapper )
                .val( value )
                .addClass( "ui-state-default ui-combobox-input input-text" )
                .autocomplete({
                    delay: 100,
                    minLength: 0,
                    dataType: "json",
                    source: function( request, response ) {
                        var term = request.term;

                        if ( term in cache ) {
                            response( cache[ term ] );
                            return;
                        }

                        var url = url_pattern
                                    .replace("{{query}}", term)
                                    .replace("{{limit}}", limit)
                                    .replace("{{page}}", page)
                                    .replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, ''));

                        $.getJSON( url, request, function( responseData, status, xhr ) {
                            cache[term] = responseData.data;
                            response( responseData.data );
                        });
                    },
                    select: function( event, ui ) {
                        ui.item.selected = true;

                        hidden.val(ui.item.id);
                        input.val(ui.item.label);
                    },
                    change: function( event, ui ) {

                        if ( !ui.item ) {
                            // remove invalid value, as it didn't match anything
                            $( this ).val( "" );
                            hidden.val( "" );
                            input.data( "autocomplete" ).term = "";
                            return false;
                        } else {
                            return true;
                        }
                    }
                })
                .addClass( "ui-widget ui-widget-content ui-corner-left" );

            if ( typeof(options.default_label) != 'undefined' ){
                input.val(options.default_label);
            }

            input.data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a>" + item.label + "</a>" )
                    .appendTo( ul );
            };

            $( "<a>" )
                .attr( "tabIndex", -1 )
                .appendTo( wrapper )
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text: false
                })
                .removeClass( "ui-corner-all" )
                .addClass( "ui-corner-right ui-combobox-toggle" )
                .click(function() {
                    // close if already visible
                    if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                        input.autocomplete( "close" );
                        return;
                    }

                    // work around a bug (likely same cause as #5265)
                    $( this ).blur();

                    // pass empty string as value to search for, displaying all results
                    input.autocomplete( "search", "" );
                    input.focus();
                });
        },

        destroy: function() {
            this.wrapper.remove();
            this.element.show();
            $.Widget.prototype.destroy.call( this );
        }
    });

})( jQuery );