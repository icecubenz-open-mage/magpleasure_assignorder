/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Common
 * @copyright  Copyright (c) 2014 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */

/**
 *
 * @param module {angular.Module}
 */

var ngColumnEditorDirective = ['$compile', function ($compile) {
    return {
        restrict: "A",
        link: function (scope, element, attrs, ctrl) {
            var column = scope.column;
            var $html = '';
            var $inner_html = '';


            if (column && column.html) {
                $inner_html = column.html;

            }
            if ($inner_html != '') {
                $html = '<div>'+$inner_html+'</div>';
                element.prepend($html);
                $compile(element.contents())(scope);
            }
            else if (!column || !column.editor || !column.editor.type) {

                var value = scope.$eval('record[column.index]');
                if (typeof(value) == "undefined" || typeof(value)== "null"){
                    value = '';
                }

                /*to do check types and convert to string. Have exception*/

                if (!Array.isArray(value))
                    element.prepend(value);
            } else {
                var $name = '"record.' + column.index + '"';

                if (column.editor.type=="checkbox"){
                    $html = '<div style="text-align:center"><input ng-model=' + $name + ' name=' + $name;
                }

                else
                    $html = '<div><input ng-model=' + $name + ' name=' + $name;

                for (var attr in column.editor) {
                    if (column.editor.hasOwnProperty(attr)) {
                        $html += ' ' + attr;
                        var value = column.editor[attr];
                        if (value && value != '') {
                            $html += '="' + value + '"';
                        }
                    }
                }
                $html += '/></div>';
                element.prepend($html);

                $compile(element.contents())(scope);
            }
        }
    }
}];

