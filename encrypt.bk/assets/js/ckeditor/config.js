/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E'; 

        config.extraPlugins = 'codesnippet,widget,dialog,widgetselection,lineutils,eqneditor,uploadimage,flowchart';//imageuploader
        config.removePlugins = 'elementspath,save,preview,print,forms,language,flash,smiley,find,replace,specialchar,newpage,templates,about,blocks,bidi';
        //config.removeButtons = 'Scayt,Copy,Cut,Paste,Undo,Redo,Print,Form,TextField,Textarea,Button,SelectAll,CreateDiv,PasteText,PasteFromWord,Select,HiddenField,Styles,Font,CopyFormatting,Indent,Outdent,Blockquote,ShowBlocks';
        config.codeSnippet_theme = 'pojoaque';
        config.toolbarGroups = [
				{"name":"others"},
				{"name":"basicstyles","groups":["basicstyles"]},
				{"name":"links","groups":["links"]},
				{"name":"paragraph","groups":["list","blocks","indent"]},
				{"name":"document","groups":["mode"]},
				{"name":"insert","groups":["insert"]},
				{"name":"styles","groups":["styles"]},
				{"name":"styles","groups":["colors"]},
			]
        config.removeButtons = 'Iframe,Anchor,PageBreak,Scayt,Copy,Cut,Paste,Undo,Redo,Print,Form,TextField,Textarea,Button,SelectAll,CreateDiv,PasteText,PasteFromWord,Select,HiddenField,Styles,CopyFormatting,Blockquote,ShowBlocks';

};
