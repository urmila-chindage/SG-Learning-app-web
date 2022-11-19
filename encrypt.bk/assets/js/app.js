var App = (function() {

	var sideBarHeight = function() {
		var winHt = $(document).height();
		$('.right-wrap').css("min-height",winHt);
	}

	var dynamicColor = function() {
		if ($('.nav-content').length) {
			$('.active-arrow').css("background","#fff");
		}
	}

	var sideBarWidth = function(){
		var width = $('.right-wrap').outerWidth();
		return width;
	}

	/* Calculating Top for content or sidebar */
	/* Based on class fixed the total height is calculated */
	var calTopFixed = function() {
		var height=0;
		$(".fixed").each(function() {
			height += $(this).outerHeight();
		});
		retHeight = height;
		return retHeight;
	}

	var returnAddHeight = function(element) {
		var getTopComm = calTopFixed(); /* Getting the top from fixed element */
		if($(element).length){
			$(element).css("top", getTopComm);
			return $(element).outerHeight();
		}else{
			return 0;
		}
	}
    
    
	var setContTop = function(topCom, cont) {

		$(".main-content").css("top",cont);
		$(".right-wrap").css("top",topCom);
	}


	var getId = function (url) {
	    var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
	    var match = url.match(regExp);

	    if (match && match[2].length == 11) {
	        return match[2];
	    } else {
	        return 'error';
	    }
	}

	/* calculate the height of two */
	var compareHeight = function (height1, height2) {
		if (height1 > height2) {
			return height1;
		}else{
			return height2;
		}
	}

	/* Calculating if the given height is greater than windows height if yes return windows height*/
	var compareWinHt = function(elemHeight){
		var winHeight = $(window).outerHeight(),
		retHt;

		if (elemHeight < winHeight) {
			retHt = winHeight;
		}else{
			retHt = elemHeight;
		}

		return retHt;
	}

	/* compare the height and return big */

	var findHeight = function (element1, element2) {
		var height1, height2, getHeight;

		height1 = $(element1).outerHeight();
		height2 = $(element2).outerHeight();
		getHeight = compareHeight(height1, height2);

		return getHeight;
	}

	var shadowPos = function () {
	var shadWidth = $('.builder-left-inner').outerWidth(), bgWidth = 342/2, imgPos = shadWidth - bgWidth;

	$('.builder-left-inner').css({"background-position":imgPos});
	}

	var tagsChange = function() {
		$('#tokenize').tokenize();
	}


	return{
		init:function(){
			// sideBarHeight();
			//dynamicColor();
		},
		contentWidth:function(content) {
			var sBWidth = sideBarWidth();
			$(content).css("padding-right", sBWidth);
		},
		calcWidth:function(content){
			var sBWidth = sideBarWidth();
			alert(sBWidth);
			var contentWidth = $(content).outerWidth();
			alert(contentWidth);
			$(content).css({width:"calc(100% - 350px)" });
			alert("calc("+contentWidth+" - "+sBWidth+")");
		},
		calcTop:function(minusElement) {
			minusElement = typeof minusElement != 'undefined' ? minusElement : 0;
			var topHeightComm = calTopFixed(); /* Height for all fixed elements - common height */
			var addHeight;
			if (minusElement != 0) {
				addHeight = returnAddHeight(minusElement);
			}else{
				addHeight = 0;
			}
			var topHeight_cont = topHeightComm + addHeight; /* Top Style for sidebar */
			setContTop(topHeightComm, topHeight_cont);
		},
		initOnChange:function(urlInput,width,height) {
			var myId;
			$(urlInput).change(function () {
			    var myUrl = $('#myUrl').val();
			    myId = getId(myUrl);
			    
			    $('#myId').html(myId);
			    
			    $('#myCode').html('<iframe width="' + width + '" height="' + height + '" src="//www.youtube.com/embed/' + myId + '" frameborder="0" allowfullscreen></iframe>');
			});
		},
		initTag:function () {
			tagsChange();
		},

		initWindowsHeight:function(element, lessHeight) {
			var winHeight = $(window).height();
			var elemHeight = winHeight - lessHeight;
			$(element).css({"max-height":elemHeight});
		},

		initWindowsHeightHeightAuto:function(element, lessHeight) {
			$(window).on('load',function() {
			
			var winHeight = $(window).height();
			var winWidth =  window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

			if (winWidth < 1450	) {
				lessHeight += 40;
			}
			// var toolbar = $(".redactor-toolbar").height();
			// alert(toolbar);
			var elemHeight = winHeight - lessHeight;
			$(element).css({"height":elemHeight, "overflow-y":"auto"});
			});
		},
		initEqualizrHeight: function(element1, element2) {
			/* Assign the returned height to the elements for content BUILDERS INNER PAGES */
			var getHeight = findHeight(element1, element2);

			/* Comparing windows height with current height and return greater */
			getHeight = compareWinHt(getHeight) + 20;

			// getHeight += 50;
			$(element1).css({"min-height":getHeight});
			$(element2).css({"min-height":getHeight});
		},
		initShadow:function () {
			shadowPos();
		}

	}

})();