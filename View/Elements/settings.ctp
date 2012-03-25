<?php echo $this->Html->useTag('fieldsetstart', __d('wysiwyg', 'Editor')); ?>
    <?php
        echo $this->Form->input('Module.settings.editor',
            array(
                'type' => 'select',
                'label' => __t('Editor'),
                'options' => array(
                    'ckeditor' => 'CKEditor',
                    'nicedit' => 'NicEdit',
                    'markitup' => 'MarkItUp',
                    'whizzywig' => 'Whizzywig',
                    'tinymce' => 'TinyMCE'
                )
            )
        );
    ?>
<?php echo $this->Html->useTag('fieldsetend'); ?>
