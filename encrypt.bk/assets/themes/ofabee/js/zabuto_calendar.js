if (typeof jQuery == "undefined") {
    throw new Error("jQuery is not loaded")
}
$.fn.zabuto_calendar = function(b) {
    var c = $.extend({}, $.fn.zabuto_calendar_defaults(), b);
    var a = $.fn.zabuto_calendar_language(c.language);
    c = $.extend({}, c, a);
    this.each(function() {
        var j = $(this);
        j.attr("id", "zabuto_calendar_" + Math.floor(Math.random() * 99999).toString(36));
        j.data("initYear", c.year);
        j.data("initMonth", c.month);
        j.data("monthLabels", c.month_labels);
        j.data("weekStartsOn", c.weekstartson);
        j.data("navIcons", c.nav_icon);
        j.data("dowLabels", c.dow_labels);
        j.data("showToday", c.today);
        j.data("showDays", c.show_days);
        j.data("showPrevious", c.show_previous);
        j.data("showNext", c.show_next);
        j.data("cellBorder", c.cell_border);
        j.data("jsonData", c.data);
        j.data("ajaxSettings", c.ajax);
        j.data("legendList", c.legend);
        j.data("actionFunction", c.action);
        j.data("actionNavFunction", c.action_nav);
        l();

        function l() {
            var y = parseInt(j.data("initYear"));
            var B = parseInt(j.data("initMonth")) - 1;
            var C = new Date(y, B, 1, 0, 0, 0, 0);
            j.data("initDate", C);
            var D = (j.data("cellBorder") === true) ? " table-bordered" : "";
            $tableObj = $('<table class="table' + D + '"></table>');
            $tableObj = w(j, $tableObj, C.getFullYear(), C.getMonth());
            $legendObj = g(j);
            var z = $('<div class="zabuto_calendar" id="' + j.attr("id") + '"></div>');
            z.append($tableObj);
            z.append($legendObj);
            j.append(z);
            var A = j.data("jsonData");
            if (false !== A) {
                r(j, C.getFullYear(), C.getMonth())
            }
        }

        function w(A, C, z, B) {
            var y = new Date(z, B, 1, 0, 0, 0, 0);
            A.data("currDate", y);
            C.empty();
            C = s(A, C, z, B);
            C = e(A, C);
            C = q(A, C, z, B);
            r(A, z, B);
            return C
        }

        function g(A) {
            var y = $('<div class="legend" id="' + A.attr("id") + '_legend"></div>');
            var z = A.data("legendList");
            if (typeof(z) == "object" && z.length > 0) {
                $(z).each(function(E, G) {
                    if (typeof(G) == "object") {
                        if ("type" in G) {
                            var F = "";
                            if ("label" in G) {
                                F = G.label
                            }
                            switch (G.type) {
                                case "text":
                                    if (F !== "") {
                                        var D = "";
                                        if ("badge" in G) {
                                            if (typeof(G.classname) === "undefined") {
                                                var H = "badge-event"
                                            } else {
                                                var H = G.classname
                                            }
                                            D = '<span class="badge ' + H + '">' + G.badge + "</span> "
                                        }
                                        y.append('<span class="legend-' + G.type + '">' + D + F + "</span>")
                                    }
                                    break;
                                case "block":
                                    if (F !== "") {
                                        F = "<span>" + F + "</span>"
                                    }
                                    if (typeof(G.classname) === "undefined") {
                                        var C = "event"
                                    } else {
                                        var C = "event-styled " + G.classname
                                    }
                                    y.append('<span class="legend-' + G.type + '"><ul class="legend"><li class="' + C + '"></li></u>' + F + "</span>");
                                    break;
                                case "list":
                                    if ("list" in G && typeof(G.list) == "object" && G.list.length > 0) {
                                        var B = $('<ul class="legend"></u>');
                                        $(G.list).each(function(J, I) {
                                            B.append('<li class="' + I + '"></li>')
                                        });
                                        y.append(B)
                                    }
                                    break;
                                case "spacer":
                                    y.append('<span class="legend-' + G.type + '"> </span>');
                                    break
                            }
                        }
                    }
                })
            }
            return y
        }

        function s(N, B, K, I) {
            var J = N.data("navIcons");
            var G = $('<span><span class="icon icon-right-open rotate"></span></span>');
            var H = $('<span><span class="icon icon-right-open"></span></span>');
            if (typeof(J) === "object") {
                if ("prev" in J) {
                    G.html(J.prev)
                }
                if ("next" in J) {
                    H.html(J.next)
                }
            }
            var M = N.data("showPrevious");
            if (typeof(M) === "number" || M === false) {
                M = p(N.data("showPrevious"), true)
            }
            var L = $('<div class="calendar-month-navigation"></div>');
            L.attr("id", N.attr("id") + "_nav-prev");
            L.data("navigation", "prev");
            if (M !== false) {
                prevMonth = (I - 1);
                prevYear = K;
                if (prevMonth == -1) {
                    prevYear = (prevYear - 1);
                    prevMonth = 11
                }
                L.data("to", {
                    year: prevYear,
                    month: (prevMonth + 1)
                });
                L.append(G);
                if (typeof(N.data("actionNavFunction")) === "function") {
                    L.click(N.data("actionNavFunction"))
                }
                L.click(function(P) {
                    w(N, B, prevYear, prevMonth)
                })
            }
            var F = N.data("showNext");
            if (typeof(F) === "number" || F === false) {
                F = p(N.data("showNext"), false)
            }
            var D = $('<div class="calendar-month-navigation"></div>');
            D.attr("id", N.attr("id") + "_nav-next");
            D.data("navigation", "next");
            if (F !== false) {
                nextMonth = (I + 1);
                nextYear = K;
                if (nextMonth == 12) {
                    nextYear = (nextYear + 1);
                    nextMonth = 0
                }
                D.data("to", {
                    year: nextYear,
                    month: (nextMonth + 1)
                });
                D.append(H);
                if (typeof(N.data("actionNavFunction")) === "function") {
                    D.click(N.data("actionNavFunction"))
                }
                D.click(function(P) {
                    w(N, B, nextYear, nextMonth)
                })
            }
            var O = N.data("monthLabels");
            var E = $("<th></th>").append(L);
            var y = $("<th></th>").append(D);
            var C = $("<span class='posrel'>" + O[I] + " " + K + "</span>");
            C.dblclick(function() {
                var P = N.data("initDate");
                w(N, B, P.getFullYear(), P.getMonth())
            });
            var z = $('<th colspan="5"></th>');
            z.append(C);
            var A = $('<tr class="calendar-month-header"></tr>');
            A.append(E, z, y);
            B.append(A);
            return B
        }

        function e(B, D) {
            if (B.data("showDays") === true) {
                var y = B.data("weekStartsOn");
                var z = B.data("dowLabels");
                if (y === 0) {
                    var C = $.extend([], z);
                    var E = new Array(C.pop());
                    z = E.concat(C)
                }
                var A = $('<tr class="calendar-dow-header"></tr>');
                $(z).each(function(F, G) {
                    A.append("<th>" + G + "</th>")
                });
                D.append(A)
            }
            return D
        }

        function q(G, F, I, N) {
            var E = G.data("ajaxSettings");
            var H = t(I, N);
            var y = o(I, N);
            var D = i(I, N, 1);
            var P = i(I, N, y);
            var C = 1;
            var B = G.data("weekStartsOn");
            if (B === 0) {
                if (P == 6) {
                    H++
                }
                if (D == 6 && (P == 0 || P == 1 || P == 5)) {
                    H--
                }
                D++;
                if (D == 7) {
                    D = 0
                }
            }
            for (var A = 0; A < H; A++) {
                var z = $('<tr class="calendar-dow"></tr>');
                for (var K = 0; K < 7; K++) {
                    if (K < D || C > y) {
                        z.append("<td></td>")
                    } else {
                        var O = G.attr("id") + "_" + k(I, N, C);
                        var M = O + "_day";
                        var L = $('<div id="' + M + '" class="day" >' + C + "</div>");
                        L.data("day", C);
                        if (G.data("showToday") === true) {
                            if (x(I, N, C)) {
                            	// Added for making today highlighted with a coloured (grey) background rounded
                            	L.addClass('today-active');
                            	// Changed for removing default background color of today (small rounded, blue color)
                                // L.html('<span class="badge badge-today">' + C + "</span>")
                                L.html('<span>' + C + "</span>")
                            }
                        }
                        var J = $('<td id="' + O + '"></td>');
                        J.append(L);
                        J.data("date", k(I, N, C));
                        J.data("hasEvent", false);
                        if (typeof(G.data("actionFunction")) === "function") {
                            J.addClass("dow-clickable");
                            J.click(function() {
                                G.data("selectedDate", $(this).data("date"))
                            });
                            J.click(G.data("actionFunction"))
                        }
                        z.append(J);
                        C++
                    }
                    if (K == 6) {
                        D = 0
                    }
                }
                F.append(z)
            }
            return F
        }

        function h(B, H, G, J) {
            var I = $('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>');
            var A = $('<h4 class="modal-title" id="' + B + '_modal_title">' + H + "</h4>");
            var K = $('<div class="modal-header"></div>');
            K.append(I);
            K.append(A);
            var F = $('<div class="modal-body" id="' + B + '_modal_body">' + G + "</div>");
            var E = $('<div class="modal-footer" id="' + B + '_modal_footer"></div>');
            if (typeof(J) !== "undefined") {
                var z = $("<div>" + J + "</div>");
                E.append(z)
            }
            var C = $('<div class="modal-content"></div>');
            C.append(K);
            C.append(F);
            C.append(E);
            var y = $('<div class="modal-dialog"></div>');
            y.append(C);
            var D = $('<div class="modal fade" id="' + B + '_modal" tabindex="-1" role="dialog" aria-labelledby="' + B + '_modal_title" aria-hidden="true"></div>');
            D.append(y);
            D.data("dateId", B);
            D.attr("dateId", B);
            return D
        }

        function r(A, z, C) {
            var y = A.data("jsonData");
            var B = A.data("ajaxSettings");
            A.data("events", false);
            if (false !== y) {
                return n(A)
            } else {
                if (false !== B) {
                    return u(A, z, C)
                }
            }
            return true
        }

        function n(z) {
            var y = z.data("jsonData");
            z.data("events", y);
            f(z, "json");
            return true
        }

        function u(z, y, C) {
            var B = z.data("ajaxSettings");
            if (typeof(B) != "object" || typeof(B.url) == "undefined") {
                alert("Invalid calendar event settings");
                return false
            }
            var A = {
                year: y,
                month: (C + 1)
            };
            $.ajax({
            	// Changed for ajax post
                // type: "GET",
                // Added post method
                type: "POST",
                url: B.url,
                data: A,
                dataType: "json"
            }).done(function(D) {

            	// Added for multiple events on the same day
                __month_data    = D;
            	var details 	= D;
            	var events 		= [];
				for(var i in details){
					var event_body 	= '';
					event_body 		= combine_events(details[i]['events']);
					if(isArray(details[i]['events'])){
						event_body 	= combine_events(details[i]['events']);
					}else{
						var item 	= details[i]['events'];
						event_body 	= '<p><b>'+item.title+'</b></p><p>'+item.message+'</p>';
					}
					var event 	= {
									"date"	: details[i].date,
									"title"	: details[i].date,
									"body"	: event_body,
								};
					events.push(event);
				}
				D 	= events;
                // Multiple events changes ends


                var E = [];
                $.each(D, function(G, F) {
                    E.push(D[G])
                });
                z.data("events", E);
                f(z, "ajax")
            });
            return true
        }



        function f(B, A) {
            var z = B.data("jsonData");
            var C = B.data("ajaxSettings");
            var y = B.data("events");
            if (y !== false) {
                $(y).each(function(H, J) {
                    var D = B.attr("id") + "_" + J.date;
                    var F = $("#" + D);
                    var K = $("#" + D + "_day");
                    F.data("hasEvent", true);
                    if (typeof(J.title) !== "undefined") {
                        F.attr("title", J.title)
                    }
                    if (typeof(J.classname) === "undefined") {
                        F.addClass("event")
                    } else {
                        F.addClass("event-styled");
                        K.addClass(J.classname)
                    }
                    if (typeof(J.badge) !== "undefined" && J.badge !== false) {
                        var E = (J.badge === true) ? "" : " badge-" + J.badge;
                        var G = K.data("day");
                        K.html('<span class="badge badge-event' + E + '">' + G + "</span>")
                    }
                    if (typeof(J.body) !== "undefined") {
                        var I = false;
                        if (A === "json" && typeof(J.modal) !== "undefined" && J.modal === true) {
                            I = true
                        } else {
                            if (A === "ajax" && "modal" in C && C.modal === true) {
                                I = true
                            }
                        }
                        if (I === true) {
                            F.addClass("event-clickable");
                            var L = h(D, J.title, J.body, J.footer);
                            $("body").append(L);
                            $("#" + D).click(function() {
                                $("#" + D + "_modal").modal()
                            })
                        }
                    }
                })
            }
        }

        function x(A, B, z) {
            var C = new Date();
            var y = new Date(A, B, z);
            return (y.toDateString() == C.toDateString())
        }

        function k(z, A, y) {
            d = (y < 10) ? "0" + y : y;
            m = A + 1;
            m = (m < 10) ? "0" + m : m;
            return z + "-" + m + "-" + d
        }

        function i(A, B, z) {
            var y = new Date(A, B, z, 0, 0, 0, 0);
            var C = y.getDay();
            if (C == 0) {
                C = 6
            } else {
                C--
            }
            return C
        }

        function o(z, A) {
            var y = 28;
            while (v(z, A + 1, y + 1)) {
                y++
            }
            return y
        }

        function t(A, C) {
            var y = o(A, C);
            var E = i(A, C, 1);
            var B = i(A, C, y);
            var D = y;
            var z = (E - B);
            if (z > 0) {
                D += z
            }
            return Math.ceil(D / 7)
        }

        function v(B, z, A) {
            return z > 0 && z < 13 && B > 0 && B < 32768 && A > 0 && A <= (new Date(B, z, 0)).getDate()
        }

        function p(A, C) {
            if (A === false) {
                A = 0
            }
            var B = j.data("currDate");
            var z = j.data("initDate");
            var y;
            y = (z.getFullYear() - B.getFullYear()) * 12;
            y -= B.getMonth() + 1;
            y += z.getMonth();
            if (C === true) {
                if (y < (parseInt(A) - 1)) {
                    return true
                }
            } else {
                if (y >= (0 - parseInt(A))) {
                    return true
                }
            }
            return false
        }
    });
    return this
};
$.fn.zabuto_calendar_defaults = function() {
    var a = new Date();
    var c = a.getFullYear();
    var e = a.getMonth() + 1;
    var b = {
        language: false,
        year: c,
        month: e,
        show_previous: true,
        show_next: true,
        cell_border: false,
        today: false,
        show_days: true,
        weekstartson: 1,
        nav_icon: false,
        data: false,
        ajax: false,
        legend: false,
        action: false,
        action_nav: false
    };
    return b
};
$.fn.zabuto_calendar_language = function(a) {
    if (typeof(a) == "undefined" || a === false) {
        a = "en"
    }
    switch (a.toLowerCase()) {
        case "de":
            return {
                month_labels: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
                dow_labels: ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"]
            };
            break;
        case "en":
            return {
                month_labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                dow_labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]
            };
            break;
        case "es":
            return {
                month_labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                dow_labels: ["Lu", "Ma", "Mi", "Ju", "Vi", "Sá", "Do"]
            };
            break;
        case "fr":
            return {
                month_labels: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
                dow_labels: ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"]
            };
            break;
        case "it":
            return {
                month_labels: ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"],
                dow_labels: ["Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom"]
            };
            break;
        case "nl":
            return {
                month_labels: ["Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"],
                dow_labels: ["Ma", "Di", "Wo", "Do", "Vr", "Za", "Zo"]
            };
            break;
        case "pt":
            return {
                month_labels: ["Janeiro", "Fevereiro", "Marco", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                dow_labels: ["S", "T", "Q", "Q", "S", "S", "D"]
            };
            break;
        case "ru":
            return {
                month_labels: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
                dow_labels: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вск"]
            };
            break;
        case "se":
            return {
                month_labels: ["Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"],
                dow_labels: ["Mån", "Tis", "Ons", "Tor", "Fre", "Lör", "Sön"]
            };
            break;
        case "tr":
            return {
                month_labels: ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],
                dow_labels: ["Pts", "Salı", "Çar", "Per", "Cuma", "Cts", "Paz"]
            };
            break
    }
};

// Added to check whether the events is array (multiple events)
function isArray(what) {
    return Object.prototype.toString.call(what) === '[object Array]';
}

// Added to combine multiple events on same day
function combine_events(events_array){
	var message 	= '';
	for(var i in events_array) {
		var item 	= events_array[i];
		message 	+= '<p><b>'+item.title+'</b></p>';
		message 	+= '<p>'+item.message+'</p>';
	}
	return message;
}