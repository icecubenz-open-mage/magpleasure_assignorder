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

var ngBindHtmlUnsafeDirective = ['$sce', function ($sce) {
    return {
        scope: {
            ngBindHtmlUnsafe: '='
        },
        template: "<div ng-bind-html='trustedHtml'></div>",
        link: function ($scope) {
            $scope.updateView = function () {
                $scope.trustedHtml = $sce.trustAsHtml($scope.ngBindHtmlUnsafe);
            }
            $scope.$watch('ngBindHtmlUnsafe', function (newValue, oldValue) {
                $scope.updateView();
            });
        }
    };
}];
