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

/**
 * Display message on frontend
 *
 * @param string  message
 * @param string|undefined  id
 */
var sendMessage = function(message, id){

    if (typeof(id) == 'undefined'){
        id = 'message';
    }

    if (!$(id)){
        $$('.col-main').each(function(el){
            var div = document.createElement('div');
            $(div).addClassName(id);
            $(div).id = id;
            Element.insert(el, {'top': div });
        });
    }
    if ($(id)){
        $(id).innerHTML = message;
    }
};

var showAdminLoading = function (show){
    changeZIndex('loading-mask', 1500);
    if ($('loading-mask')){
        $('loading-mask').style.display = show ? 'block' : 'none';
    }
};

var changeZIndex = function(id, z){
    if ($(id)){
        $(id).style.zIndex = z;
    }
};

var addHiddenFiledToForm = function(formId, fieldName, value){
    if ($(formId)){
        var input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', fieldName);
        input.setAttribute('value', value);
        $(formId).appendChild(input);
    }
};

var disabledEventPropagation = function(event){
    if (event.stopPropagation){
        event.stopPropagation();
    } else if(window.event){
        window.event.cancelBubble=true;
    }
};