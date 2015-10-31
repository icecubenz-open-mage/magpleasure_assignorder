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

var ngShowRequiredDirective = ['$compile', function ($compile) {
    return {
        restrict: 'E',
        scope: true,
        replace: true,
        template: ' <div class="validation-advice error" ng-show="({{name}}.$dirty || submitted) && {{name}}.$error.required">This is a required field.</div>'
    }

}];

