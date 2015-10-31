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

var MpAdminhtmlWidgetAjaxForm = Class.create();
MpAdminhtmlWidgetAjaxForm.prototype = {
    initialize:function (params) {
        this.data = {};
        for (key in params) {
            this[key] = this.data[key] = params[key];
        }

        if (!$(this.form_container_id)){
            var div = jQuery('<div style="display: none;"></div>')
                        .attr('id', this.form_container_id)
                        .addClass(this.form_container_class)
                        ;

            jQuery('body').prepend(div);
        }
    },
    _debug: function(data){
        if (this.use_debug){
            console.log(data);
        }
    },
    open: function(id){
        this._debug('Ajax Form: Load form');

        if (!id){
            id = 0;
        }

        this.entity_id = id;

        /*
         *  Load Dialog Content
         */
        new Ajax.Request(
            this.load_url.replace('{{entity_id}}', id).replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')), {
                method: 'post',
                parameters: this.data,
                onSuccess: (function(transport){
                    if (transport && transport.responseText) {
                        try {
                            var response = eval('(' + transport.responseText + ')');

                            if (response.expired){

                                setLocation(response.redirect);

                            } else if (response.success){

                                if (typeof(angular) != "undefined"){

                                    var elem = angular.element(document.body);
                                    var injector = elem.injector();
                                    var $compile = injector.get('$compile');
                                    var $rootScope = injector.get('$rootScope');
                                    var elem = $compile(response.html)($rootScope);

                                    $rootScope.$digest();


                                    if ($(this.html_id)){
                                        jQuery('#' + this.html_id).html(elem);
                                    }

                                    /**
                                     * Dialog
                                     */
                                    jQuery('#' + this.html_id).dialog({
                                        autoOpen: true,
                                        height: this.height,
                                        width: this.width,
                                        modal: true,
                                        title: response.title,
                                        buttons: this.buttons,
                                        close: (function(){
                                            this.onClose();
                                        }).bind(this),
                                        open: (function(){
                                            this.onLoad();
                                        }).bind(this)
                                    });


                                } else {


                                    if ($(this.html_id)){
                                        jQuery('#' + this.html_id).html(response.html);
                                    }

                                    if (typeof response.html.evalScripts == 'function') {
                                        response.html.evalScripts();
                                    }

                                    /**
                                     * Dialog
                                     */
                                    jQuery('#' + this.html_id).dialog({
                                        autoOpen: true,
                                        height: this.height,
                                        width: this.width,
                                        modal: true,
                                        title: response.title,
                                        buttons: this.buttons,
                                        close: (function(){
                                            this.onClose();
                                        }).bind(this),
                                        open: (function(){
                                            this.onLoad();
                                        }).bind(this)
                                    });

                                }



                            } else {
                                sendMessage(response.messages, 'messages');
                            }
                        } catch (e) {
                            this._debug(e);
                            sendMessage(this.error, 'messages');
                        }
                    } else {
                        sendMessage(this.error, 'messages');
                    }
                }).bind(this)
            });

    },
    close: function(){
        this._debug('Ajax Form: Close form');

        jQuery('#' + this.html_id).dialog("close");
    },
    save: function(){
        this._debug('Ajax Form: Form save');

        this._debug('try to validate ' + this.html_id + 'Form');

        var form = new varienForm(this.html_id + 'Form');
        var validator = form.validator;
        if (form && validator && validator.validate()){

            showAdminLoading(true);

            /*
             *  Submit Form
             */
            new Ajax.Request(
                this.save_url.replace('{{entity_id}}', this.entity_id).replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')), {
                    method: 'post',
                    parameters:  $(this.html_id + 'Form').serialize(),
                    onSuccess: (function(transport){
                        if (transport && transport.responseText) {
                            try {
                                var response = eval('(' + transport.responseText + ')');
                                if (response.expired){
                                    setLocation(response.redirect);
                                } else if (response.success){
                                    sendMessage(response.messages, 'messages');
                                    jQuery('#' + this.html_id).dialog("close");
                                    this.onSave();
                                } else {
                                    sendMessage(response.messages ? response.messages : this.error, 'ajax_form_message');
                                }
                            } catch (e) {
                                sendMessage(this.error, 'ajax_form_message');
                            }
                        } else {
                            sendMessage(this.error, 'ajax_form_message');
                        }
                    }).bind(this)
                });
        }
    },
    onClose: function(){
        this._debug('Ajax Form: Close Callback');
        if (this.closeCallback){
            if (typeof(this.closeCallback) == 'function'){
                this.closeCallback();
            }
        }
    },
    onSave: function(){
        this._debug('Ajax Form: Save Callback');
        if (this.saveCallback){
            if (typeof(this.saveCallback) == 'function'){
                this.saveCallback();
            }
        }
    },
    onLoad: function(){
        this._debug('Ajax Form: Load Callback');
        if (this.loadCallback){
            if (typeof(this.loadCallback) == 'function'){
                this.loadCallback();
            }
        }
    }

};