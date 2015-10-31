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


var MpAdminhtmlWidgetFormTree = Class.create();
MpAdminhtmlWidgetFormTree.prototype = {
    initialize:function (params) {

        // Default data
        this.use_debug = false;
        this.selected = [];
        this.orig_selected = [];
        this.dialog = false;
        this.cache = [];

        for (key in params) {
            this[key] = params[key];
        }

        this.root_id = this.root_data.node_id;

        if ($(this.label_id)){

            $(this.label_id).observe('mousemove', (function(e){

                this.displaySelector(true);
                disabledEventPropagation(e);

            }).bind(this));

            $(this.label_id).observe('click', (function(){

                // Open when click from tablet
                this.open();

            }).bind(this));
        }

        if ($(this.selector_id)){

            $(this.selector_id).observe('mousemove', (function(e){

                this.displaySelector(true);
                disabledEventPropagation(e);

            }).bind(this));

            $(this.selector_id).observe('click', (function(){

                this.open();

            }).bind(this));
        }


        if ($(document)){
            $(document).observe('mousemove', (function(){

                this.displaySelector(false);
            }).bind(this));
        }

        if (this.tree){
            this.tree.on('check', (function(node, checked) {
                this.checked(node, checked);
            }).bind(this), this.tree);
        }



        this.selected = $(this.input_id).value.split(",");

        this.render();
        this.render();

    },
    _debug: function(data){
        if (this.use_debug){
            console.log(data);
        }
    },
    _cleanTree: function(node){

        if (typeof(node) != 'undefined'){

            node.checked = false;
            if (typeof(node.children) != 'undefined'){
                node.children.each((function(el){
                    this._cleanTree(el);
                }).bind(this));
            }

        } else {

            this._cleanTree(this.root_data);
            this.leafs_data.each((function(el){
                this._cleanTree(el);
            }).bind(this));
        }
    },
    _expandNode: function(node){

        if (typeof node.parent_id != 'undefined'){

            if (node.parent_id != this.root_id){

                var parent = this._findNode(node.parent_id);
                if (!parent.expanded){
                    this._expandNode(parent);
                }
            }
        }

        node.expanded = true;
    },
    _findNode: function(id, startFromNode){

        if (id == ''){
            return false;
        }

        if (typeof(this.cache[id]) != 'undefined'){
            return this.cache[id];
        }

        if (typeof(startFromNode) != 'undefined'){

            if (typeof(startFromNode.children) != 'undefined'){

                startFromNode.children.each((function(el){

                    this.cache[el.node_id] = el;
                    if (el.node_id == id){
                        return el;
                    } else {
                        return this._findNode(id, el);
                    }
                }).bind(this).bind(id));
            }
        } else {
            this.cache[this.root_data.node_id] = this.root_data;
            if (this.root_data.node_id == id){

                return this.root_data;
            } else {

                this.leafs_data.each((function(el){

                    this.cache[el.node_id] = el;

                    if (el.node_id == id){
                        return el;
                    } else {

                        return this._findNode(id, el);
                    }
                }).bind(this).bind(id));
            }

        }
        return false;
    },
    selectedToTree: function(){
        this._debug('Abstract Tree: Selected to Tree.');

        this._cleanTree();

        this.selected.each((function(id){

            // Get Node to Proceed

            if (id && (id != '')) {
                var node = this._findNode(id);
                if (node) {
                    node.checked = true;

                    // Expand checked nodes
                    this._expandNode(node);
                }
            }
        }).bind(this));
    },
    checked: function(node, checked){
        this._debug('Abstract Tree: Check ' + node.id + ' ' + ((checked) ? 'true' : 'false'));

        var id = node.id;
        if (checked){

            if (this.selected.indexOf(id) == -1){
                this.selected.push(id);
            }
        } else {

            if (this.selected.indexOf(id) != -1){
                this.selected = this.selected.without(id);
            }
        }
    },
    open: function(){

        this._debug('Abstract Tree: Open Tree List.');

        var timer = setTimeout(function(){
            showAdminLoading(true);
            timer = false;
        }, 500);

        // Reset data on tree
        this.orig_selected = this.selected.clone();
        this.selectedToTree();
        this.selectedToTree();
        this.tree.loadTree({parameters:this.root_data, data:this.leafs_data}, true);


        // Open the window
        jQuery('#' + this.content_id).dialog({
            modal: true,
            width: 400,
            height: 400,
            title: this.title,
            buttons: this.buttons,
            open: (function(e){
                if (timer){
                    clearTimeout(timer);
                } else {
                    showAdminLoading(false);
                }

            }).bind(timer)
        });

    },
    close: function(){
        this._debug('Abstract Tree: Close window.');
        jQuery('#' + this.content_id).dialog("close");
        this.selected = this.orig_selected.clone();
        this.selectedToTree();
    },
    apply: function(){
        this._debug('Abstract Tree: Apply tree items.');
        jQuery('#' + this.content_id).dialog("close");
        $(this.input_id).value = this.selected.join(',');
        this.render();
    },
    render: function(){
        this._debug('Abstract Tree: Render Node Names.');

        var labels = [];

        this.selected.each((function(id){

            var node = this._findNode(id);
            if (node){
                labels.push(node.text);
            }

        }).bind(this).bind(labels));

        var label = $(this.label_id);
        if (label){
            label.innerHTML = labels.join(', ');
        }
    },
    displaySelector: function(show){
        if (show){
            $(this.selector_id).setStyle({display: 'block'});
            ///TODO Add animation in future

        } else {
            $(this.selector_id).setStyle({display: 'none'});
            ///TODO Add animation in future

        }
    }
};