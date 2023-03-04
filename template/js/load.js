/*
jquery.percentageloader.js 
 
Copyright (c) 2012, Better2Web
All rights reserved.

This jQuery plugin is licensed under the Simplified BSD License. Please
see the file license.txt that was included with the plugin bundle.

*/

/*global jQuery */

(function ($) {
    /* Strict mode for this plugin */
    "use strict";
    /*jslint browser: true */

    /* Our spiral gradient data */
    var imgdata = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAIAAABMXPacAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6N0JGOThBMkEyODMyMTFFN0JDMkFBQzUzRjdCMzY0QUEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6N0JGOThBMkIyODMyMTFFN0JDMkFBQzUzRjdCMzY0QUEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo3QkY5OEEyODI4MzIxMUU3QkMyQUFDNTNGN0IzNjRBQSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3QkY5OEEyOTI4MzIxMUU3QkMyQUFDNTNGN0IzNjRBQSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PlBlMLMAABUSSURBVHja1F1L2uM6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",
        gradient = new Image();
    gradient.src = imgdata;

    /** Percentage loader
     * @param	params	Specify options in {}. May be on of width, height, progress or value.
     *
     * @example $("#myloader-container).percentageLoader({
		    width : 256,  // width in pixels
		    height : 256, // height in pixels
		    progress: 0,  // initialise progress bar position, within the range [0..1]
		    value: '0kb'  // initialise text label to this value
		});
     */
    $.fn.percentageLoader = function (params) {
        var settings, canvas, percentageText, valueText, items, i, item, selectors, s, ctx, progress,
            value, cX, cY, lingrad, innerGrad, tubeGrad, innerRadius, innerBarRadius, outerBarRadius,
            radius, startAngle, endAngle, counterClockwise, completeAngle, setProgress, setValue,
            applyAngle, drawLoader, clipValue, outerDiv;

        /* Specify default settings */
        settings = {
            width: 128,
            height: 128,
            progress: 0,
            value: '0kb',
            controllable: false
        };

        /* Override default settings with provided params, if any */
        if (params !== undefined) {
            $.extend(settings, params);
        } else {
            params = settings;
        }

        outerDiv = document.createElement('div');
        outerDiv.style.width = settings.width + 'px';
        outerDiv.style.height = settings.height + 'px';
        outerDiv.style.position = 'relative';

        $(this).append(outerDiv);

        /* Create our canvas object */
        canvas = document.createElement('canvas');
        canvas.setAttribute('width', settings.width);
        canvas.setAttribute('height', settings.height);
        outerDiv.appendChild(canvas);

        /* Create div elements we'll use for text. Drawing text is
         * possible with canvas but it is tricky working with custom
         * fonts as it is hard to guarantee when they become available
         * with differences between browsers. DOM is a safer bet here */
        percentageText = document.createElement('div');
        percentageText.style.width = (settings.width.toString() - 2) + 'px';
        percentageText.style.textAlign = 'center';
        percentageText.style.height = '50px';
        percentageText.style.left = 0;
        percentageText.style.position = 'absolute';

        valueText = document.createElement('div');
        valueText.style.width = (settings.width - 2).toString() + 'px';
        valueText.style.textAlign = 'center';
        valueText.style.height = '0px';
        valueText.style.overflow = 'hidden';
        valueText.style.left = 0;
        valueText.style.position = 'absolute';

        /* Force text items to not allow selection */
        items = [valueText, percentageText];
        for (i  = 0; i < items.length; i += 1) {
            item = items[i];
            selectors = [
                '-webkit-user-select',
                '-khtml-user-select',
                '-moz-user-select',
                '-o-user-select',
                'user-select'];

            for (s = 0; s < selectors.length; s += 1) {
                $(item).css(selectors[s], 'none');
            }
        }

        /* Add the new dom elements to the containing div */
        outerDiv.appendChild(percentageText);
        outerDiv.appendChild(valueText);

        /* Get a reference to the context of our canvas object */
        ctx = canvas.getContext("2d");


        /* Set various initial values */

        /* Centre point */
        cX = (canvas.width / 2) - 1;
        cY = (canvas.height / 2) - 1;

        /* Create our linear gradient for the outer ring */
        lingrad = ctx.createLinearGradient(cX, 0, cX, canvas.height);
        lingrad.addColorStop(0, '#eee');
        lingrad.addColorStop(1, '#eee');

        /* Create inner gradient for the outer ring */
        innerGrad = ctx.createLinearGradient(cX, cX * 0.133333, cX, canvas.height - cX * 0.133333);
        innerGrad.addColorStop(0, '#fafafa');
        innerGrad.addColorStop(1, '#fafafa');

        /* Tube gradient (background, not the spiral gradient) */
        tubeGrad = ctx.createLinearGradient(cX, 0, cX, canvas.height);
        tubeGrad.addColorStop(0, '#fff');
        tubeGrad.addColorStop(1, '#fafafa');

        /* The inner circle is 2/3rds the size of the outer one */
        innerRadius = cX * 0.6666;
        /* Outer radius is the same as the width / 2, same as the centre x
        * (but we leave a little room so the borders aren't truncated) */
        radius = cX - 2;

        /* Calculate the radii of the inner tube */
        innerBarRadius = innerRadius + (cX * 0.06);
        outerBarRadius = radius - (cX * 0.06);

        /* Bottom left angle */
        startAngle = 2.1707963267949;
        /* Bottom right angle */
        endAngle = 0.9707963267949 + (Math.PI * 2.0);

        /* Nicer to pass counterClockwise / clockwise into canvas functions
        * than true / false */
        counterClockwise = false;

        /* Borders should be 1px */
        ctx.lineWidth = 1;

        /**
         * Little helper method for transforming points on a given
         * angle and distance for code clarity
         */
        applyAngle = function (point, angle, distance) {
            return {
                x : point.x + (Math.cos(angle) * distance),
                y : point.y + (Math.sin(angle) * distance)
            };
        };


        /**
         * render the widget in its entirety.
         */
        drawLoader = function () {
            /* Clear canvas entirely */
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            /*** IMAGERY ***/

            /* draw outer circle */
            ctx.fillStyle = lingrad;
            ctx.beginPath();
            ctx.strokeStyle = '#ccc';
            ctx.arc(cX, cY, radius, 0, Math.PI * 2, counterClockwise);
            ctx.fill();
            ctx.stroke();

            /* draw inner circle */
            ctx.fillStyle = innerGrad;
            ctx.beginPath();
            ctx.arc(cX, cY, innerRadius, 0, Math.PI * 2, counterClockwise);
            ctx.fill();
            ctx.strokeStyle = '#ccc';
            ctx.stroke();

            ctx.beginPath();

            /**
             * Helper function - adds a path (without calls to beginPath or closePath)
             * to the context which describes the inner tube. We use this for drawing
             * the background of the inner tube (which is always at 100%) and the
             * progress meter itself, which may vary from 0-100% */
            function makeInnerTubePath(startAngle, endAngle) {
                var centrePoint, startPoint, controlAngle, capLength, c1, c2, point1, point2;
                centrePoint = {
                    x : cX,
                    y : cY
                };

                startPoint = applyAngle(centrePoint, startAngle, innerBarRadius);

                ctx.moveTo(startPoint.x, startPoint.y);

                point1 = applyAngle(centrePoint, endAngle, innerBarRadius);
                point2 = applyAngle(centrePoint, endAngle, outerBarRadius);

                controlAngle = endAngle + (3.142 / 2.0);
                /* Cap length - a fifth of the canvas size minus 4 pixels */
                capLength = (cX * 0.20) - 4;

                c1 = applyAngle(point1, controlAngle, capLength);
                c2 = applyAngle(point2, controlAngle, capLength);

                ctx.arc(cX, cY, innerBarRadius, startAngle, endAngle, false);
                ctx.bezierCurveTo(c1.x, c1.y, c2.x, c2.y, point2.x, point2.y);
                ctx.arc(cX, cY, outerBarRadius, endAngle, startAngle, true);

                point1 = applyAngle(centrePoint, startAngle, innerBarRadius);
                point2 = applyAngle(centrePoint, startAngle, outerBarRadius);

                controlAngle = startAngle - (3.142 / 2.0);

                c1 = applyAngle(point2, controlAngle, capLength);
                c2 = applyAngle(point1, controlAngle, capLength);

                ctx.bezierCurveTo(c1.x, c1.y, c2.x, c2.y, point1.x, point1.y);
            }

            /* Background tube */
            ctx.beginPath();
            ctx.strokeStyle = '#ccc';
            makeInnerTubePath(startAngle, endAngle);

            ctx.fillStyle = tubeGrad;
            ctx.fill();
            ctx.stroke();

            /* Calculate angles for the the progress metre */
            completeAngle = startAngle + (progress * (endAngle - startAngle));

            ctx.beginPath();
            makeInnerTubePath(startAngle, completeAngle);

            /* We're going to apply a clip so save the current state */
            ctx.save();
            /* Clip so we can apply the image gradient */
            ctx.clip();

            /* Draw the spiral gradient over the clipped area */
            ctx.drawImage(gradient, 0, 0, canvas.width, canvas.height);

            /* Undo the clip */
            ctx.restore();

            /* Draw the outline of the path */
            ctx.beginPath();
            makeInnerTubePath(startAngle, completeAngle);
            ctx.stroke();

            /*** TEXT ***/
            (function () {
                var fontSize, string, smallSize, heightRemaining;
                /* Calculate the size of the font based on the canvas size */
                fontSize = cX / 2;

                percentageText.style.top = '38px';
                percentageText.style.color = '#555';
                percentageText.style.font = fontSize.toString() + 'px nav';
                percentageText.style.textShadow = '0 1px 1px #fff';

                /* Calculate the text for the given percentage */
                string = (progress * 100.0).toFixed(0) + '%';

                percentageText.innerHTML = string;

                /* Calculate font and placement of small 'value' text */
                smallSize = cX / 5.5;
                valueText.style.color = '#191919';
                valueText.style.font = smallSize.toString() + 'px nav';
                valueText.style.height = smallSize.toString() + 'px';
                valueText.style.textShadow = 'None';

                /* Ugly vertical align calculations - fit into bottom ring.
                 * The bottom ring occupes 1/6 of the diameter of the circle */
                heightRemaining = (settings.height * 0.16666666) - smallSize;
                valueText.style.top = '88px';
            }());
        };

        /**
        * Check the progress value and ensure it is within the correct bounds [0..1]
        */
        clipValue = function () {
            if (progress < 0) {
                progress = 0;
            }

            if (progress > 1.0) {
                progress = 1.0;
            }
        };

        /* Sets the current progress level of the loader
         *
         * @param value the progress value, from 0 to 1. Values outside this range
         * will be clipped
         */
        setProgress = function (value) {
            /* Clip values to the range [0..1] */
            progress = value;
            clipValue();
            drawLoader();
        };

        this.setProgress = setProgress;

        setValue = function (val) {
            value = val;
            valueText.innerHTML = value;
        };

        this.setValue = setValue;
        this.setValue(settings.value);

        progress = settings.progress;
        clipValue();

        /* Do an initial draw */
        drawLoader();

        /* In controllable mode, add event handlers */
        if (params.controllable === true) {
            (function () {
                var mouseDown, getDistance, adjustProgressWithXY;
                getDistance = function (x, y) {
                    return Math.sqrt(Math.pow(x - cX, 2) + Math.pow(y - cY, 2));
                };

                mouseDown = false;

                adjustProgressWithXY = function (x, y) {
                    /* within the bar, calculate angle of touch point */
                    var pX, pY, angle, startTouchAngle, range, posValue;
                    pX = x - cX;
                    pY = y - cY;

                    angle = Math.atan2(pY, pX);
                    if (angle > Math.PI / 2.0) {
                        angle -= (Math.PI * 2.0);
                    }

                    startTouchAngle = startAngle - (Math.PI * 2.0);
                    range = endAngle - startAngle;
                    posValue = (angle - startTouchAngle) / range;
                    setProgress(posValue);

                    if (params.onProgressUpdate) {
                        /* use the progress value as this will have been clipped
                         * to the correct range [0..1] after the call to setProgress
                         */
                        params.onProgressUpdate(progress);
                    }
                };

                $(outerDiv).mousedown(function (e) {
                    var offset, x, y, distance;
                    offset = $(this).offset();
                    x = e.pageX - offset.left;
                    y = e.pageY - offset.top;

                    distance = getDistance(x, y);

                    if (distance > innerRadius && distance < radius) {
                        mouseDown = true;
                        adjustProgressWithXY(x, y);
                    }
                }).mouseup(function () {
                    mouseDown = false;
                }).mousemove(function (e) {
                    var offset, x, y;
                    if (mouseDown) {
                        offset = $(outerDiv).offset();
                        x = e.pageX - offset.left;
                        y = e.pageY - offset.top;
                        adjustProgressWithXY(x, y);
                    }
                }).mouseleave(function () {
                    mouseDown = false;
                });
            }());
        }
        return this;
    };
}(jQuery));
