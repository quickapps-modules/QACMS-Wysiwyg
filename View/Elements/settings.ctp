<?php
    echo $this->Form->input('Module.settings.editor',
        array(
            'type' => 'radio',
            'label' => __d('wysiwyg', 'Editor'),
            'separator' => '<br />',
            'options' => array(
                'ckeditor' => 'CKEditor',
                'nicedit' => 'NicEdit',
                'markitup' => 'MarkItUp',
                'whizzywig' => 'Whizzywig',
                'tinymce' => 'TinyMCE'
            )
        )
    );