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


var mpTinyMceWysiwygSetup = Class.create();
mpTinyMceWysiwygSetup.prototype = {
    initialize: function(htmlId, config)
    {
        this.id = htmlId;
        this.config = config;
        varienGlobalEvents.attachEventHandler('tinymceChange', this.onChangeContent.bind(this));

        if(typeof tinyMceEditors == 'undefined') {
            tinyMceEditors = $H({});
        }

        tinyMceEditors.set(this.id, this);
    },
    onChangeContent: function() {
        // Add "changed" to tab class if it exists
//        if(this.config.tab_id) {
//            var tab = $$('a[id$=' + this.config.tab_id + ']')[0];
//            if ($(tab) != undefined && $(tab).hasClassName('tab-item-link')) {
//                $(tab).addClassName('changed');
//            }
//        }


        ///TODO Register changes
    }



};