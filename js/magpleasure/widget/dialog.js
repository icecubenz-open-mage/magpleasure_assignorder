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

var MpWidgetDialog = Class.create();
MpWidgetDialog.prototype = {
    initialize:function (params) {
        for (key in params) {
            this[key] = params[key];
        }

        $(this['dialog-container']).observe('click', (function(e){
            e.stopPropagation();
        }).bind(this));

        $(document).observe('click', (function(e){
            this.close();
        }).bind(this));

        $(document).observe('keypress', (function(e){
            if (e.keyCode == 27){
                this.close();
            }
        }).bind(this));
    },
    buttonClick: function(){
        this.showOverlay(true, (function(){
            this.showLoader(true);

            /*
             *  Load Dialog Content
             */
            new Ajax.Request(
                this.window_url
                    .replace('{{width}}', this.width)
                    .replace('{{height}}', this.height)
                    .replace('{{post_url}}', this.post_url)
                    .replace('{{forward_data}}', this.forward_data)
                    .replace('{{additional_data}}', this.additional_data)
                    .replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')), {
                    method: 'get',
                    onSuccess: (function(transport){
                        if (transport && transport.responseText) {
                            try {
                                var response = eval('(' + transport.responseText + ')');
                                if (!response.error && response.html) {
                                    this.showLoader(false, (function(){

                                        this.showDialog(response);

                                    }).bind(this));
                                } else {
                                    this.showLoader(false, (function(){
                                        this.showOverlay(false, (function(){
                                            if (response.message){
                                                sendMessage(response.message);
                                            }
                                        }).bind(response));
                                    }).bind(this).bind(response));
                                }
                            } catch (e) {
                                response = {};
                            }
                        }
                    }).bind(this),
                    onFailure: (function(transport){
                        this.showLoader(false, (function(){
                            this.showOverlay(false, (function(){
                                sendMessage(this.error_message);
                            }).bind(this))
                        }).bind(this));
                    }).bind(this)
                });
        }).bind(this));
    },
    close: function(){
        $$('.mp-dialog-container.active').each((function(el){
            this.showInstance(el.id, false, (function(){
                this.showOverlay(false);
            }).bind(this));
        }).bind(this));
    },
    hideDialog: function(callback){
        this.showInstance(this['dialog-container'], false, callback);
    },
    showDialog: function(response, callback){

        if ($(this['dialog-content'])){
            $(this['dialog-content']).innerHTML = response.html;
            response.html.evalScripts();
        }

        var divs = ['mp-dialog-container', 'mp-dialog-wrapper', 'mp-dialog-content'];
        for (var i = 0; i < divs.length; i++){
            var id = divs[i];
            if ($(id)){
                $(id).style.width = response.width;
                $(id).style.height = response.height;
            }
        }

        this.showInstance(this['dialog-container'], true, callback);
    },
    showOverlay: function(show, callback){
        this.showInstance(this.overlay, show, callback);
    },
    showLoader: function(show, callback){
        this.showInstance(this.loader, show, callback);
    },
    showInstance: function(id, show, callback){
        if ($(id)){
            if (show){
                $(id).addClassName('active');
                Effect.Appear(id, {duration: 0.3, afterFinish: (function(){
                    if (typeof(callback) == 'function'){
                        callback();
                    }
                }).bind(callback)});
            } else {
                $(id).removeClassName('active');
                Effect.Fade(id, {duration: 0.2, afterFinish: (function(){
                    if (typeof(callback) == 'function'){
                        callback();
                    }
                }).bind(callback)});
            }
        }
    },
    save: function(form){
        this.showLoader(true);

        /*
         *  Post Dialog Form
         */
        new Ajax.Request(
            form.action.replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')), {
            method: 'post',
            parameters: $(form.id).serialize(true),
            onSuccess: (function(transport){
                if (transport && transport.responseText) {
                    try {
                        var response = eval('(' + transport.responseText + ')');
                        this.showLoader(false);
                        if (response.success){
                            this.hideDialog((function(){
                                this.showOverlay(false, (function(){
                                    if (response.message){
                                        sendMessage(response.message);
                                    }
                                }).bind(this).bind(response));
                            }).bind(this).bind(response));
                        }
                        if (response.error){
                            if (response.message){
                                /* Send Message to Dialog Window */
                                sendMessage(response.message, 'mp-dialog-message');
                            }
                            return false;
                        }
                    } catch (e) {
                        this.showLoader(false);
                    }
                }
            }).bind(this),
            onFailure: (function(transport){
                this.showLoader(false, (function(){
                    sendMessage(this.error_message);
                }).bind(this));
            }).bind(this)
        });

        return false;
    }
};