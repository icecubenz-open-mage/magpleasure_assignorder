/*
 *
 * https://github.com/fatlinesofcode/ngDraggable
 */

(function () {


    angular.module("ngDraggable", [])
        .directive('ngDrag', ['$rootScope', '$parse', '$timeout', function ($rootScope, $parse, $timeout) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    scope.value = attrs.ngDrag;
                    //  return;
                    var offset, _mx, _my, _tx, _ty;
                    var _hasTouch = ('ontouchstart' in document.documentElement);
                    var _pressEvents = 'touchstart mousedown';
                    var _moveEvents = 'touchmove mousemove';
                    var _releaseEvents = 'touchend mouseup';

                    var $document = $(document);
                    var $window = $(window);
                    var _data = null;
                    var _element = null;
                    var _attrs = attrs;
                    var _dragEnabled = false;

                    var _pressTimer = null;

                    var onDragBeginCallback = $parse(attrs.ngDragBegin) || null;
                    var onDragSuccessCallback = $parse(attrs.ngDragSuccess) || null;
                    var onDragCancelCallback = $parse(attrs.ngDragCancel) || null;

                    var initialize = function () {
                        element.attr('draggable', 'false'); // prevent native drag
                        toggleListeners(true);
                    };

                    var toggleListeners = function (enable) {
                        // remove listeners

                        if (!enable) {
                            return;
                        }
                        // add listeners

                        scope.$on('$destroy', onDestroy);
                        attrs.$observe("ngDrag", onEnableChange);
                        scope.$watch(attrs.ngDragData, onDragDataChange);
                        element.on(_pressEvents, onpress);
                        if (!_hasTouch) {
                            jQuery(_element).on('mousedown', function () {
                                return false;
                            }); // prevent native drag
                        }
                    };
                    var onDestroy = function (enable) {
                        toggleListeners(false);
                    };
                    var onDragDataChange = function (newVal, oldVal) {
                        _data = newVal;
                    };
                    var onEnableChange = function (newVal, oldVal) {
                        _dragEnabled = scope.$eval(newVal);

                    };
                    /*
                     * When the element is clicked start the drag behaviour
                     * On touch devices as a small delay so as not to prevent native window scrolling
                     */
                    var onpress = function (evt) {
                        if (!_dragEnabled) {
                            return;
                        }

                        if (_hasTouch) {
                            cancelPress();
                            _pressTimer = $timeout.setTimeout(function () {
                                cancelPress();
                                onlongpress(evt);
                            }, 100);
                            jQuery(document).on(_moveEvents, cancelPress);
                            jQuery(document).on(_releaseEvents, cancelPress);
                        } else {
                            onlongpress(evt);
                        }

                    };
                    var cancelPress = function () {
                        $timeout.cancel(_pressTimer);
                        jQuery(document).off(_moveEvents, cancelPress);
                        jQuery(document).off(_releaseEvents, cancelPress);
                    };
                    var onlongpress = function (evt) {
                        if (!_dragEnabled) {
                            return;
                        }
                        evt.preventDefault();

                        _element = element[0];

                        offset = _element.getBoundingClientRect();
                        _element.centerX = offset.width / 2;
                        _element.centerY = offset.height / 2;

                        jQuery(_element).addClass('dragging'); // This is a question

                        _mx = (evt.pageX || evt.originalEvent.touches[0].pageX);
                        _my = (evt.pageY || evt.originalEvent.touches[0].pageY);

                        _tx = _mx - _element.centerX - jQuery(window).scrollLeft();
                        _ty = _my - _element.centerY - jQuery(window).scrollTop();

                        _element.attrs = _attrs;

                        moveElement(_tx, _ty);

                        jQuery(document).on(_moveEvents, onmove);
                        jQuery(document).on(_releaseEvents, onrelease);

                        $rootScope.$broadcast('draggable:start', {
                            x: _mx,
                            y: _my,
                            tx: _tx,
                            ty: _ty,
                            element: _element,
                            data: _data
                        });

                        onDragBegin(evt);
                    };
                    var onmove = function (evt) {

                        if (!_dragEnabled) {
                            return;
                        }

                        evt.preventDefault();

                        _mx = (evt.pageX || evt.originalEvent.touches[0].pageX);
                        _my = (evt.pageY || evt.originalEvent.touches[0].pageY);
                        _tx = _mx - _element.centerX - jQuery(window).scrollLeft();
                        _ty = _my - _element.centerY - jQuery(window).scrollTop();

                        moveElement(_tx, _ty);

                        $rootScope.$broadcast('draggable:move', {
                            x: _mx,
                            y: _my,
                            tx: _tx,
                            ty: _ty,
                            element: _element,
                            data: _data
                        });

                    };
                    var onrelease = function (evt) {
                        if (!_dragEnabled) {
                            return;
                        }

                        evt.preventDefault();

                        $rootScope.$broadcast('draggable:end', {
                            x: _mx,
                            y: _my,
                            tx: _tx,
                            ty: _ty,
                            element: _element,
                            data: _data,
                            attrs: _attrs,
                            callback: onDragComplete
                        });

                        jQuery(_element).removeClass('dragging');

                        reset();

                        jQuery(document).off(_moveEvents, onmove);
                        jQuery(document).off(_releaseEvents, onrelease);

                        onDragCancel(evt);
                    };

                    var onDragBegin = function (evt) {

                        if (!onDragBeginCallback) {
                            return;
                        }

                        scope.$apply(function () {
                            onDragBeginCallback(scope, {$data: _data, $attrs: _attrs, $event: evt});
                        });
                    };

                    var onDragComplete = function (evt) {

                        if (!onDragSuccessCallback) {
                            return;
                        }

                        scope.$apply(function () {
                            onDragSuccessCallback(scope, {$data: _data, $attrs: _attrs, $event: evt});
                        });
                    };

                    var onDragCancel = function (evt) {

                        if (!onDragCancelCallback) {
                            return;
                        }

                        scope.$apply(function () {
                            onDragCancelCallback(scope, {$data: _data, $attrs: _attrs, $event: evt});
                        });
                    };

                    var reset = function () {
                        jQuery(_element).css({left: '', top: '', position: '', 'z-index': ''});
                    };

                    var moveElement = function (x, y) {

                        jQuery(_element).css({left: x, top: y, position: 'fixed', 'z-index': 99999});
                    };

                    initialize();
                }
            }
        }])
        .directive('ngDrop', ['$parse', '$timeout', function ($parse, $timeout) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    scope.value = attrs.ngDrop;

                    var _dropEnabled = false;
                    var _element = element[0];
                    var _attrs = attrs;

                    var onDropCallback = $parse(attrs.ngDropSuccess);// || function(){};
                    var initialize = function () {
                        toggleListeners(true);
                    };

                    var toggleListeners = function (enable) {
                        // remove listeners

                        if (!enable)return;
                        // add listeners.
                        attrs.$observe("ngDrop", onEnableChange);
                        scope.$on('$destroy', onDestroy);
                        //scope.$watch(attrs.uiDraggable, onDraggableChange);
                        scope.$on('draggable:start', onDragStart);
                        scope.$on('draggable:move', onDragMove);
                        scope.$on('draggable:end', onDragEnd);
                    };
                    var onDestroy = function (enable) {
                        toggleListeners(false);
                    };
                    var onEnableChange = function (newVal, oldVal) {
                        _dropEnabled = scope.$eval(newVal);
                    }
                    var onDragStart = function (evt, obj) {
                        if (!_dropEnabled)return;
                        isTouching(obj.x, obj.y, obj.element);
                    }
                    var onDragMove = function (evt, obj) {
                        if (!_dropEnabled)return;
                        isTouching(obj.x, obj.y, obj.element);
                    }
                    var onDragEnd = function (evt, obj) {
                        if (!_dropEnabled)return;
                        if (isTouching(obj.x, obj.y, obj.element)) {
                            // call the ngDraggable element callback
                            if (obj.callback) {
                                obj.callback(evt);
                            }

                            // call the ngDrop element callback
                            //   scope.$apply(function () {
                            //       onDropCallback(scope, {$data: obj.data, $event: evt});
                            //   });
                            $timeout(function () {
                                onDropCallback(scope, {$data: obj.data, $attrs: _attrs, $oAttrs: obj.attrs, $event: evt});
                            });
                        }
                        updateDragStyles(false, obj.element);
                    }
                    var isTouching = function (mouseX, mouseY, dragElement) {
                        var touching = hitTest(mouseX, mouseY);
                        updateDragStyles(touching, dragElement);
                        return touching;
                    }
                    var updateDragStyles = function (touching, dragElement) {
                        if (touching) {
                            jQuery(element).addClass('drag-enter');
                            jQuery(dragElement).addClass('drag-over');
                        } else {
                            jQuery(element).removeClass('drag-enter');
                            jQuery(dragElement).removeClass('drag-over');
                        }
                    }
                    var hitTest = function (x, y) {
                        var bounds = element.offset();
                        bounds.right = bounds.left + element.outerWidth();
                        bounds.bottom = bounds.top + element.outerHeight();
                        return x >= bounds.left
                        && x <= bounds.right
                        && y <= bounds.bottom
                        && y >= bounds.top;
                    }

                    initialize();
                }
            }
        }])
        .directive('ngPreventDrag', ['$parse', '$timeout', function ($parse, $timeout) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    var initialize = function () {

                        jQuery(element[0]).attr('draggable', 'false');
                        toggleListeners(true);
                    };


                    var toggleListeners = function (enable) {
                        if (!enable) {
                            return;
                        }
                        jQuery(element[0]).on('mousedown touchstart touchmove touchend touchcancel', absorbEvent_);
                    };

                    var absorbEvent_ = function (event) {
                        var e = event.originalEvent;
                        e.preventDefault && e.preventDefault();
                        e.stopPropagation && e.stopPropagation();
                        e.cancelBubble = true;
                        e.returnValue = false;
                        return false;
                    }

                    initialize();
                }
            }
        }]);

})();