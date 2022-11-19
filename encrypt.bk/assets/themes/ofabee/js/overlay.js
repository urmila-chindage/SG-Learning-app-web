
(function($, d, w, u) {
	
	var Overlay = {
		body: $('body'),
		init: function(elem, data, config, created, closed) {
			var self = this;
			self.elem = elem;
			self.data = data;
			self.created = created;
			self.closed = closed;
			self.conf = $.extend({}, $.fn.overlay.config, config);
			$( elem ).click(function(e) {
				e.preventDefault();
				self.createBackground.call(self);
			});
		},
		
		createBackground: function() {
			var self = this;
			self.myBg = $('<div />', {
				class: self.conf.bgClass
			}).appendTo(self.body)
				.css({'background': 'rgba(0, 0, 0, 0.50)',
					  'display': 'none',
					  'position': 'fixed',
					  'top': 0,
					  'left': 0,
					  'z-index': 9999,
					  'height': '100%',
					  'width': '100%',
					  'overflow': 'auto'
					 }).fadeIn(self.conf.duration);
			self.createContainer(); 
		},
		
		createContainer: function() {
			var self = this;
			self.myContainer = $('<div />', {
				class: self.conf.boxClass
			})
			.appendTo(self.myBg);
			self.addData();
			self.appendCloseBtn();
			var boxHeight = self.myContainer.height(),
				winHeight = $( w ).height(),
				boxMarginTop = (winHeight - boxHeight) / 3;
			if(boxMarginTop < 0) {
				boxMarginTop = 0;
			}
			self.myContainer.css("margin-top", boxMarginTop);
			if(self.created) {
				self.created.call(self.elem, self);
			}
			
		},
		
		addData: function() {
			var self = this;
			self.myContainer.html(self.data);
		},
		
		appendCloseBtn: function() {
			var self = this;
			self.closeBtn = $('<a />', {
				href: '#',
				class: self.conf.closeBtnClass,
				text: self.conf.closeBtnText})
				.appendTo(self.myContainer);
			self.bindClose();
		},
		
		bindClose: function() {
			var self = this;
			self.myBg.add(self.closeBtn).click(function(e) {
				if(e.target == this) {
					e.preventDefault();
					self.close.call(self);
				}
			});
		},
		
		close: function() {
			var self = this;
			self.myBg.fadeOut(self.conf.duration, function() {
				$(this).remove();
				if(self.closed) {
					self.closed.call(self.elem, self);
				}
			});
		}
		
	};
	
	$.fn.overlay = function(data, config, created, closed) {
		return $.each(this, function() {
			var overlay = Object.create(Overlay);
				overlay.init(this, data, config, created, closed);
		});
	};
	
	$.fn.overlay.config = {
		initialWidth: 200,
		duration: 100,
		bgClass: 'bg', 
		bgColor: 'rgba(0,0,0,.45)',
		boxClass: 'overlay-box',
		closeBtnClass: 'overlay-closeBtn',
		closeBtnText: 'x'
	}
	
})(jQuery, document, window);