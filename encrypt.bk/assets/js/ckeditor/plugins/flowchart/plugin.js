CKEDITOR.plugins.add('flowchart',
{
    init: function (editor) {
        var pluginName = 'flowchart';
        editor.ui.addButton('Flowchart',
            {
                label: 'Flowchart',
                command: 'OpenWindow',
                icon: CKEDITOR.plugins.getPath('flowchart') + '/images/flowchart.jpg'
            });
        var cmd = editor.addCommand('OpenWindow', { exec: flowchartPopUp });
    }
});
