var drawing_svg_script = function (svg_from_db) {
	"use strict";

	var	svgExport,
		lines = [],
        rect,
        ellipse,
		shape,
		mouseDown,
		nested,
        shapeNested,
		resizeMouseDown,
		paint,
		resizer,
		strokeSize = 1,
		color = '#000',
		offset =$('#svg-container').offset(),
		currentShapeId = null,
		svgContainer = SVG('svg-container').size(640, 360),
        svgContainerId = svgContainer.node.id;
        
    //check if svg is coming for db, if it is import that code
    if (svg_from_db != undefined)
        svgContainer.svg(svg_from_db);

	var drawingTool = {		
		init: function(){
			var self = this;

            $('.pen').addClass('current-tool');
			$('.move').removeClass('current-tool');
			$('.rect').removeClass('current-tool');
            $('.ellipse').removeClass('current-tool');

			$('.move').on('click', function () {
				$(this).addClass('current-tool');
				$('.pen').removeClass('current-tool');
				$('.rect').removeClass('current-tool');
                $('.ellipse').removeClass('current-tool');

                self.pen.unbindEvents();
                self.shape.unbindEvents();
                self.draggableOn();
                // adds remove method on every shape and activates the closeX button  
				self.remove();
			});

			$('.pen').on('click', function () {
				$(this).addClass('current-tool');
				$('.move').removeClass('current-tool');
				$('.rect').removeClass('current-tool');
                $('.ellipse').removeClass('current-tool');
                
				self.pen.bindEvents();
				self.shape.unbindEvents();
                self.draggableOff();
			});

			$('.rect').on('click', function () {
				$(this).addClass('current-tool');
				$('.move').removeClass('current-tool');
				$('.pen').removeClass('current-tool');
                $('.ellipse').removeClass('current-tool');
                shape = 'rect';

                self.pen.unbindEvents();
                // if shape does not have the events listner bind them, avoiding binding the events multiple times
                if (!self.shape.bindedEvents)
				    self.shape.bindEvents();
                self.draggableOff();
			});

			$('.ellipse').on('click', function () {
				$(this).addClass('current-tool');
				$('.move').removeClass('current-tool');
				$('.pen').removeClass('current-tool');
				$('.rect').removeClass('current-tool');
                shape = 'ellipse';

                self.pen.unbindEvents();
                // if shape does not have the events listner bind them, avoiding binding the events multiple times
                if (!self.shape.bindedEvents)
				    self.shape.bindEvents();
                self.draggableOff();
			});

			$('#current-color').on('mousedown', function (event) {
				event.preventDefault();
				
				$('.flyout-colors').show();
				$('.black').on('mouseup', function (){
					color ='#000000';
					self.updateShape()
					
					$('.flyout-colors').hide();
					$('#current-color img').attr("src", "img/black.png");
				});
				$('.flyout-colors').show();
				$('.blue').on('mouseup', function (){
					color ='#0000FF';
					self.updateShape()
					
					$('.flyout-colors').hide();
					$('#current-color img').attr("src", "img/blue.png");
				});
				$('.green').on('mouseup', function (){
					color ='#008000';
					self.updateShape()

					$('.flyout-colors').hide();
					$('#current-color img').attr("src", "img/green.png");
				});
				$('.red').on('mouseup', function (){
					color = '#FF0000';
					self.updateShape()

					$('.flyout-colors').hide();
					$('#current-color img').attr("src", "img/red.png");

				});
				$('.yellow').on('mouseup', function (){
					color ='#FFFF00';
					self.updateShape();

					$('.flyout-colors').hide();
					$('#current-color img').attr("src", "img/yellow.png");

				});
			}).on('mouseup',function () {
				$('.flyout-colors').hide();;
			});

			$('#current-stroke').on('mousedown', function (event) {
				event.preventDefault();
				
				$('.flyout-stroke').show();
				$('.stroke-one').on('mouseup', function (){
					strokeSize = 1;
					self.updateShape();					

					$('.flyout-stroke').hide();
					$('#current-stroke img').attr("src", "img/stroke-one.png");
				});
				$('.stroke-two').on('mouseup', function (){
					strokeSize = 2;
					self.updateShape();

					$('.flyout-stroke').hide();
					$('#current-stroke img').attr("src", "img/stroke-two.png");
				});
				$('.stroke-three').on('mouseup', function (){
					strokeSize = 3;
					self.updateShape();			
	
					$('.flyout-stroke').hide();
					$('#current-stroke img').attr("src", "img/stroke-three.png");
				});
			}).on('mouseup',function () {
				$('.flyout-stroke').hide();;
			});

            $('.clear').on('mousedown', function (){
				svgContainer.clear();
				$(this).addClass('current-tool');
            }).on('mouseup', function (){
				$(this).removeClass('current-tool');
            });
            // default tool
            self.pen.bindEvents();
		},

		updateShape: function() {
            var lines = $('#' + currentShapeId).find('line');
            lines.each(function () {
                $(this).attr({'stroke': color,'stroke-width': strokeSize});
            });

            var rect = $('#' + currentShapeId).find('rect');
            rect.attr({'stroke': color, 'stroke-width': strokeSize});
        },

        draggableOn: function(){ 
            var self = this,
                svgContainerChildren = svgContainer.children();

            // Make all shapes draggable
            for (var i = 0; i < svgContainerChildren.length; i++)
            {
                var child_id = svgContainerChildren[i].node.id;
                SVG.get(child_id).draggable();
                
                if ( $('#'+child_id).find('image').attr('id') != undefined )
                    $('#'+child_id).find('image').on('mouseenter', function(e) {
					    $(this).css('cursor','nwse-resize');
                    }).on('mousedown',function(){
                        SVG.get(currentShapeId).draggable(false);
                    }).on('mouseup', function (){
                        SVG.get(currentShapeId).draggable();
                    });
            }
            self.resize.bindEvents();

            SVG.get(svgContainerId).on('click', function (event) {
                var $target = $(event.target);
                var nodeName =  $target.context.nodeName;

                if (nodeName == 'line')
                {
                    currentShapeId = $target.parent().attr('id');
                    self.editOn();
                }
                else if (nodeName == 'rect')
                {
                    currentShapeId = $target.parent().attr('id');
                    self.editOn();
                }
                else if (nodeName == 'ellipse')
                {
                    currentShapeId = $target.parent().attr('id');
                    self.editOn();
                }
                else {
                    currentShapeId = null;
                }
            });	
        },

        draggableOff: function(){
            var svgContainerChildren = svgContainer.children();
            for (var i = 0; i < svgContainerChildren.length; i++)
            {
                var child_id = svgContainerChildren[i].node.id;
                $('#'+child_id).off();
                SVG.get(child_id).draggable(false);
            }
        },

        editOff: function(){
            var childNodes = $('#'+ svgContainerId ).children();

            childNodes.each(function () {
                $(this).attr({'opacity': 1});
                $(this).find('path').hide();
                $(this).find('image').hide();
            });
        },

        editOn: function(){
            this.editOff();
            var current = $('#' + currentShapeId);

            current.attr({'opacity': 0.5, 'cursor': 'move'});
            current.find('path').show();
            current.find('ellipse').show();	
           
            if ( current.find('image').length != 0)
            {
                var imgsId = current.find('image').attr('id');
                var imgHref = current.find('image').attr('href');
                //load resize.png image
                SVG.get(imgsId).load(imgHref);
                current.find('image').show();
            }
        },

		remove: function() {
			var closeX = $('#'+svgContainerId).find('path');
            // console.log(closeX);
			closeX.each(function () {
				var imageId = $(this).attr('id')
				var parentNodeId = $(this).parent().attr('id');
				SVG.get(imageId).on('mouseover', function () {
					SVG.get(imageId).style('cursor','pointer');
				})
				.on('click', function () {
					// SVG.get(imageId).remove();
					SVG.get(parentNodeId).remove();
				});
			});
		},

        pen: { 
            bindEvents: function () {
                var self = this;
                svgContainer.on('mousedown', self.mouseDown);
                svgContainer.on('mousemove', self.mouseMove);
                svgContainer.on('mouseup', self.mouseUp);
                this.bindedEvents = true;
                // console.log('pen.bindedEvents', this.bindedEvents);
            },

            bindedEvents: true,

            mouseDown: function(event){
                var self = this;
                // drawindTool.editOff();
                nested = svgContainer.nested();
                currentShapeId = nested.node.id;

                paint = true;
                var id = 1;

                lines[id] = {
                    // x: event.pageX - offset.left, 
                    // y: event.pageY - offset.top, 
                    // color : color
                    x: event.pageX - offset.left , 
                    y: event.pageY - offset.top,
                    color : color
                };
                //Uses path to place a X close button at the top-right corner of every shape drawn.
                nested.path("m 147.79678,382.56724 c 8.41311,7.7574 8.70322,8.16569 8.70322,8.16569 l -5.22193,-4.49113 -2.90108,4.08284 6.67247,-8.16568 -3.77139,3.67456 z").stroke({width:1.5,color:'#ff0000'}).center(lines[id].x, lines[id].y).attr({'display':'none'});
            },

            mouseMove: function(event) {
                if (paint){
                    var id = 1,
                        moveX = event.pageX - offset.left  - lines[id].x,
                        moveY = event.pageY - offset.top - lines[id].y;
                        
                    var ret = drawingTool.pen.move(id, moveX, moveY);
                    lines[id].x = ret.x;
                    lines[id].y = ret.y;
                    // SVG.get('SvgjsSvg1000').style('cursor','auto');
                }
            },

            mouseUp: function(){
                paint = false;
            },

            // mouseOut: function (event) {
            // 	var handler = document.getElementById('svg-container');
            // 	function isMouseLeaveOrEnter(event, handler){		
            // 		if (e.type != 'mouseout' && e.type != 'mouseover') return false;
            // 		var reltg = e.relatedTarget ? e.relatedTarget :
            // 		e.type == 'mouseout' ? e.toElement : e.fromElement;
            // 		while (reltg && reltg != handler) reltg = reltg.parentNode;
            // 		return (reltg != handler);
            // 	}
            // 	if (isMouseLeaveOrEnter) {
            // 		paint = false;
            // 		console.log('isMouseLeave', paint);
            // 	}
            // },	

            isMouseLeaveOrEnter: function (event, handler) {
            },

            move: function(i, changeX, changeY){
                nested.line(lines[i].x, lines[i].y, lines[i].x + changeX, lines[i].y + changeY)
                .attr({
                    'stroke-width':	strokeSize,
                     stroke: lines[i].color
                });
                return {
                    x: lines[i].x + changeX,
                    y: lines[i].y + changeY
                };
            },

            unbindEvents: function () {
                var self = this;
                // svgContainer.off();
                svgContainer.off('mousedown', self.mouseDown);
                svgContainer.off('mousemove', self.mouseMove);
                svgContainer.off('mouseup', self.mouseUp);
                this.bindedEvents = false;
                // console.log('pen.bindedEvents', this.bindedEvents);
            }
		},
		
        shape: { 
            bindEvents: function () {
                var self = this;
                svgContainer.on("mousedown", self.mouseDown);
                svgContainer.on("mousemove", self.mouseMove);
                svgContainer.on("mouseup", self.mouseUp);
                this.bindedEvents = true;
            },

            bindedEvents: false,

            mouseDown: function(event){
                shapeNested = svgContainer.nested();
                currentShapeId = shapeNested.node.id;
                
                mouseDown = true;

                var	x1 = event.pageX - offset.left,
                    y1 = event.pageY - offset.top;
                //stores starting point (x1, y1) 
                shapeNested.move(x1,y1);
                
                if (shape == 'rect'){
                    rect = shapeNested.rect(1,1);
                    rect.attr({stroke:color, 'stroke-width':strokeSize, fill:'rgba(123,111,233,0)'});
                }
                if (shape == 'ellipse'){
                    ellipse = shapeNested.ellipse(1,1);
                    ellipse.attr({stroke:color, 'stroke-width':strokeSize, fill:'rgba(123,111,233,0)'});
                }
            },

            mouseMove: function(event){
                if (mouseDown)
                {   // ending point x2, y2
                    // console.log( $._data( $('svg#SvgjsSvg1000'), 'events') );
                    
                    var	x2 = event.pageX - offset.left - shapeNested.attr('x'),
                        y2 = event.pageY - offset.top - shapeNested.attr('y'),
                        transform_shape
                    if (shape == 'rect')
                        transform_shape = rect;
                    if (shape == 'ellipse')
                        transform_shape = ellipse;

                    if(x2 > 0 && y2 > 0)
                    {
                        transform_shape.matrix('1,0,0,1,0,0');
                        if (shape == 'rect')
                            transform_shape.size(x2, y2);
                        if (shape == 'ellipse')
                            transform_shape.radius(x2, y2);
                    }
                    else if(x2 > 0 && y2 < 0)
                    {
                        transform_shape.matrix('1,0,0,-1,0,0');
                        if (shape == 'rect')
                            transform_shape.size(x2, Math.abs(y2));
                        if (shape == 'ellipse')
                            transform_shape.radius(x2, Math.abs(y2));
                    }
                    else if(x2 < 0 && y2 > 0 )
                    {
                        transform_shape.matrix('-1,0,0,1,0,0');
                        if (shape == 'rect')
                            transform_shape.size(Math.abs(x2), y2);
                        if (shape == 'ellipse')
                            transform_shape.radius(Math.abs(x2), y2);
                    }
                    else if (x2 < 0 && y2 < 0)
                    {
                        transform_shape.matrix('-1,0,0,-1,0,0');
                        if (shape == 'rect')
                            transform_shape.size(Math.abs(x2), Math.abs(y2));
                        if (shape == 'ellipse')
                            transform_shape.radius(Math.abs(x2), Math.abs(y2));
                    }	
                }
            },

            mouseUp: function (event) {
                mouseDown = false;

                // places a X close button at the top-right corner of shape
                shapeNested.path("m 147.79678,382.56724 c 8.41311,7.7574 8.70322,8.16569 8.70322,8.16569 l -5.22193,-4.49113 -2.90108,4.08284 6.67247,-8.16568 -3.77139,3.67456 z").stroke({width:1.5, color:'#ff0000'}).center(0,0).attr({'display':'none'});

                // places a resizing image at the bottom-right corner of shape
                shapeNested.image('/img/resize.png').center(0,0).move(event.pageX - offset.left - shapeNested.attr('x'), event.pageY - offset.top - shapeNested.attr('y')).attr({'display':'none'});
            },

            unbindEvents: function () {
                var self = this;
                this.bindedEvents = false;
                svgContainer.off('mousedown', self.mouseDown);
                svgContainer.off('mousemove', self.mouseMove);
                svgContainer.off('mouseup', self.mouseUp);
            }
		},

		resize: { 
            bindEvents: function () {
                svgContainer.on('mousedown',this.mouseDown);
                svgContainer.on('mousemove',this.mouseMove);
                svgContainer.on('mouseup',this.mouseUp);
            },
            mouseDownOnResize: false,

            mouseDown: function (event) {
                var $target = $(event.target),
                    nodeName = $target.context.nodeName;
                if (nodeName == 'image')
                {
                    this.mouseDownOnResize = true;
                }	
            },

            mouseMove: function (event) {
                var $resizer, $shape;

                if (this.mouseDownOnResize)
                {
                    var movX = (event.pageX - offset.left - SVG.get(currentShapeId).attr('x')),
                        movY = (event.pageY - offset.top - SVG.get(currentShapeId).attr('y'));
                    
                    SVG.get(currentShapeId).each(function (i, children) {
                        if (this.node.nodeName == 'image')
                            $resizer = SVG.get(this.node.id); 
                        if (this.node.nodeName == 'rect' || this.node.nodeName == 'ellipse')
                            $shape = SVG.get(this.node.id);
                    });

                    $resizer.move(movX, movY);
                    if(movX > 0 && movY > 0)
                    {
                        $shape.matrix('1,0,0,1,0,0');
                        if ($shape.node.nodeName == 'rect')
                            $shape.size(movX, movY);
                        if ($shape.node.nodeName == 'ellipse')
                            $shape.radius(movX, movY);
                    }
                    else if(movX > 0 && movY < 0)
                    {
                        $shape.matrix('1,0,0,-1,0,0');
                        if ($shape.node.nodeName == 'rect')
                            $shape.size(movX, Math.abs(movY));
                        if ($shape.node.nodeName == 'ellipse')
                            $shape.radius(movX, Math.abs(movY));
                    }
                    else if(movX < 0 && movY > 0 )
                    {
                        $shape.matrix('-1,0,0,1,0,0');
                        if ($shape.node.nodeName == 'rect')
                            $shape.size(Math.abs(movX), movY);
                        if ($shape.node.nodeName == 'ellipse')
                            $shape.radius(Math.abs(movX), movY);
                    }
                    else if (movX < 0 && movY < 0)
                    {
                        $shape.matrix('-1,0,0,-1,0,0');
                        if ($shape.node.nodeName == 'rect')
                            $shape.size(Math.abs(movX), Math.abs(movY));
                        if ($shape.node.nodeName == 'ellipse')
                            $shape.radius(Math.abs(movX), Math.abs(movY));
                    }
                }
            },

            mouseUp: function () {
                this.mouseDownOnResize = false;
            },

            unbindEvents: function () {
                svgContainer.off('mousedown',this.mouseDown);
                svgContainer.off('mousemove',this.mouseMove);
                svgContainer.off('mouseup',this.mouseUp);
            }
		}
	};
		
	window.App.Drawing.Export = function () {
		return svgContainer;
	};
    window.drawingToolUnbindEvents = function(){
        drawingTool.pen.unbindEvents();
        drawingTool.shape.unbindEvents();
    };
    window.svg_container = svgContainer;
    window.draggableOn = function() {
        drawingTool.draggableOn();
    };
	return drawingTool.init();
};

function hasHorizontalScroll(node){
    if(node == undefined)
        if(window.innerWidth)
            return document.body.offsetWidth> innerWidth;
        else 
            return  document.documentElement.scrollWidth > document.documentElement.offsetWidth || document.body.scrollWidth > document.body.offsetWidth;
    else 
        return node.scrollWidth> node.offsetWidth;
}
