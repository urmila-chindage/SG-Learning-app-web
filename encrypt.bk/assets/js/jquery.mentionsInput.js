(function ($, _, undefined) {

    // Settings
    var KEY = { BACKSPACE : 8, TAB : 9, RETURN : 13, ESC : 27, LEFT : 37, UP : 38, RIGHT : 39, DOWN : 40, COMMA : 188, SPACE : 32, HOME : 36, END : 35 }; // Keys "enum"
    var defaultSettings = {
            triggerChar   : '@',
            onDataRequest : $.noop,
            minChars      : 2,
            showAvatars   : true,
            classes       : {
        autoCompleteItemActive : "active"
    },
    templates     : {
        wrapper                    : _.template('<div class="mentions-input-box"></div>'),
        autocompleteList           : _.template('<div class="mentions-autocomplete-list"></div>'),
        autocompleteListItem       : _.template('<li data-ref-id="<%= id %>" data-ref-type="<%= type %>" data-display="<%= display %>"><%= content %></li>'),
        autocompleteListItemAvatar : _.template('<img  src="<%= avatar %>" />'),
        autocompleteListItemIcon   : _.template('<div class="icon <%= icon %>"></div>'),
        mentionItemSyntax          : _.template('@[<%= value %>](<%= type %>:<%= id %>)'),
        mentionItemHighlight       : _.template('<strong><span><%= value %></span></strong>')
    }
    };

    var utils = {
            htmlEncode       : function (str) {
        return _.escape(str);
    },
    highlightTerm    : function (value, term) {
        if (!term && !term.length) {
            return value;
        }
        return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<b>$1</b>");
    },
    setCaratPosition : function (domNode, caretPos) {
        if (domNode.createTextRange) {
            var range = domNode.createTextRange();
            range.move('character', caretPos);
            range.select();
        } else {
            if (domNode.selectionStart) {
                domNode.focus();
                domNode.setSelectionRange(caretPos, caretPos);
            } else {
                domNode.focus();
            }
        }
    },
    rtrim: function(string) {
        return string.replace(/\s+$/,"");
    }
    };

    var MentionsInput = function (input) {
        var settings;
        var elmInputBox, elmInputWrapper, elmAutocompleteList, elmWrapperBox, elmActiveAutoCompleteItem;
        var mentionsCollection = [];
        var inputBuffer = [];
        var currentDataQuery;

        function initTextarea() {
            elmInputBox = $(input);

            if (elmInputBox.attr('data-mentions-input') == 'true') {
                return;
            }

            elmInputWrapper = elmInputBox.parent();
            elmWrapperBox = $(settings.templates.wrapper());
            elmInputBox.wrapAll(elmWrapperBox);
            elmWrapperBox = elmInputWrapper.find('> div');

            elmInputBox.attr('data-mentions-input', 'true');
        }

        function initAutocomplete() {
            elmAutocompleteList = $(settings.templates.autocompleteList());
            elmAutocompleteList.appendTo(elmWrapperBox);
            elmAutocompleteList.delegate('li', 'click', onAutoCompleteItemClick);
        }

        function resetBuffer() {
            inputBuffer = [];
        }

        function updateMentionsCollection() {
            var inputText = getInputBoxValue();

            mentionsCollection = _.reject(mentionsCollection, function (mention, index) {
                return !mention.value || inputText.indexOf(mention.value) == -1;
            });
            mentionsCollection = _.compact(mentionsCollection);
        }

        function addMention(value, id, type) {
            var currentMessage = getInputBoxValue();
            var findString = settings.triggerChar + currentDataQuery;

            // Using a regex to figure out positions
            var regex = new RegExp("\\" + findString, "gi");
            regex.exec(currentMessage);

            var startCaretPosition = regex.lastIndex - currentDataQuery.length - 1;
            var currentCaretPosition = regex.lastIndex;

            var start = currentMessage.substr(0, startCaretPosition);
            var end = currentMessage.substr(currentCaretPosition, currentMessage.length);
            var startEndIndex = (start + value).length;

            var updatedMessageText = start + value + end;

            mentionsCollection.push({
                id    : id,
                type  : type,
                value : value
            });

            // Cleaning before inserting the value, otherwise auto-complete would be triggered with "old" inputbuffer
            resetBuffer();
            currentDataQuery = '';
            hideAutoComplete();

            var editor = CKEDITOR.instances.editor1;
            var sel = editor.getSelection();


            var element = sel.getStartElement();
            sel.selectElement(element);

            var ranges = editor.getSelection().getRanges();

            var nodeList = element.getChildren();
            for ( i =0; i < nodeList.count(); i++) {
                var elementChild = nodeList.getItem(i);
                var startIndex = elementChild.getText().toLowerCase().indexOf(findString.toLowerCase());

                if (startIndex != -1) {
                    ranges[0].setStart(elementChild, startIndex);
                    ranges[0].setEnd(elementChild, startIndex + findString.length);
                    sel.selectRanges([ranges[0]]);

                    var range = sel.getRanges()[0];
                    range.deleteContents();
                    range.select();

                    editor.insertHtml(value);
                }
            }
            editor.updateElement();

        }

        function getInputBoxValue() {
            return $.trim(CKEDITOR.instances.editor1.getData());
        }

        function onAutoCompleteItemClick(e) {
            var elmTarget = $(this);

            addMention(elmTarget.attr('data-display'), elmTarget.attr('data-ref-id'), elmTarget.attr('data-ref-type'));

            return false;
        }

        window.onInputBoxClick = function (e) {
            resetBuffer();
        }

        window.onInputBoxInput = function (e) {
            updateMentionsCollection();
            hideAutoComplete();

            var triggerCharIndex = _.lastIndexOf(inputBuffer, settings.triggerChar);
            if (triggerCharIndex > -1) {
                currentDataQuery = inputBuffer.slice(triggerCharIndex + 1).join('');
                currentDataQuery = utils.rtrim(currentDataQuery);

                _.defer(_.bind(doSearch, this, currentDataQuery));
            }
        }

        window.onInputBoxKeyPress = function (e) {
            var keyCode = (e.data.keyCode === undefined ? e.data.getKey() : e.data.keyCode);
            var typedValue = String.fromCharCode(keyCode);
            inputBuffer.push(typedValue);
        }

        window.onInputBoxKeyDown = function (e) {
            var keyCode = (e.data.keyCode === undefined ? e.data.getKey() : e.data.keyCode);

            // This also matches HOME/END on OSX which is CMD+LEFT, CMD+RIGHT
            if (keyCode == KEY.LEFT || keyCode == KEY.RIGHT || keyCode == KEY.HOME || keyCode == KEY.END) {
                // Defer execution to ensure carat pos has changed after HOME/END keys
                _.defer(resetBuffer);
                return;
            }

            if (keyCode == KEY.BACKSPACE) {
                inputBuffer = inputBuffer.slice(0, -1 + inputBuffer.length); // Can't use splice, not available in IE
                return;
            }

            if (!elmAutocompleteList.is(':visible')) {
                return true;
            }

            switch (keyCode) {
            case KEY.UP:
            case KEY.DOWN:
                var elmCurrentAutoCompleteItem = null;
                if (keyCode == KEY.DOWN) {
                    if (elmActiveAutoCompleteItem && elmActiveAutoCompleteItem.length) {
                        elmCurrentAutoCompleteItem = elmActiveAutoCompleteItem.next();
                    } else {
                        elmCurrentAutoCompleteItem = elmAutocompleteList.find('li').first();
                    }
                } else {
                    elmCurrentAutoCompleteItem = $(elmActiveAutoCompleteItem).prev();
                }

                if (elmCurrentAutoCompleteItem.length) {
                    selectAutoCompleteItem(elmCurrentAutoCompleteItem);
                }
                e.data.preventDefault();

                return false;

            case KEY.RETURN:
            case KEY.TAB:
                if (elmActiveAutoCompleteItem && elmActiveAutoCompleteItem.length) {
                    elmActiveAutoCompleteItem.click();
                    e.data.preventDefault();
                    return false;
                }

                break;
            }

            return true;
        }

        function hideAutoComplete() {
            elmActiveAutoCompleteItem = null;
            elmAutocompleteList.empty().hide();
        }

        function selectAutoCompleteItem(elmItem) {
            elmItem.addClass(settings.classes.autoCompleteItemActive);
            elmItem.siblings().removeClass(settings.classes.autoCompleteItemActive);

            elmActiveAutoCompleteItem = elmItem;
        }

        function populateDropdown(query, results) {
            elmAutocompleteList.show();

            // Filter items that has already been mentioned
            var mentionValues = _.pluck(mentionsCollection, 'value');
            results = _.reject(results, function (item) {
                return _.include(mentionValues, item.name);
            });

            if (!results.length) {
                hideAutoComplete();
                return;
            }

            elmAutocompleteList.empty();
            var elmDropDownList = $("<ul>").appendTo(elmAutocompleteList).hide();

            _.each(results, function (item, index) {
                var elmListItem = $(settings.templates.autocompleteListItem({
                    'id'      : utils.htmlEncode(item.id),
                    'display' : utils.htmlEncode(item.name),
                    'type'    : utils.htmlEncode(item.type),
                    'content' : utils.highlightTerm(utils.htmlEncode((item.name)), query)
                }));

                if (index === 0) { 
                    selectAutoCompleteItem(elmListItem); 
                }

                if (settings.showAvatars) {
                    var elmIcon;

                    if (item.avatar) {
                        elmIcon = $(settings.templates.autocompleteListItemAvatar({ avatar : item.avatar }));
                    } else {
                        elmIcon = $(settings.templates.autocompleteListItemIcon({ icon : item.icon }));
                    }
                    elmIcon.prependTo(elmListItem);
                }
                elmListItem = elmListItem.appendTo(elmDropDownList);
            });

            elmAutocompleteList.show();
            elmDropDownList.show();
        }

        function doSearch(query) {
            if (query && query.length && query.length >= settings.minChars) {
                settings.onDataRequest.call(this, 'search', query, function (responseData) {
                    populateDropdown(query, responseData);
                });
            }
        }

        // Public methods
        return {
            init : function (options) {
            settings = options;

            initTextarea();
            initAutocomplete();
        },

        val : function (callback) {
            if (!_.isFunction(callback)) {
                return;
            }

            var value = mentionsCollection.length ? elmInputBox.data('messageText') : getInputBoxValue();
            callback.call(this, value);
        },

        reset : function () {
            elmInputBox.val('');
            mentionsCollection = [];
        },

        getMentions : function (callback) {
            if (!_.isFunction(callback)) {
                return;
            }

            callback.call(this, mentionsCollection);
        }
        };
    };

    $.fn.mentionsInput = function (method, settings) {

        if (typeof method === 'object' || !method) {
            settings = $.extend(true, {}, defaultSettings, method);
        }

        var outerArguments = arguments;

        return this.each(function () {
            var instance = $.data(this, 'mentionsInput') || $.data(this, 'mentionsInput', new MentionsInput(this));

            if (_.isFunction(instance[method])) {
                return instance[method].apply(this, Array.prototype.slice.call(outerArguments, 1));

            } else if (typeof method === 'object' || !method) {
                return instance.init.call(this, settings);

            } else {
                $.error('Method ' + method + ' does not exist');
            }

        });
    };

})(jQuery, _);