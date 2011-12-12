<?php echo $this->Html->useTag('fieldsetstart', __d('wysiwyg', 'Editor')); ?>
    <?php
        echo $this->Form->input('Module.settings.editor',
            array(
                'type' => 'select',
                'label' => __t('Editor'),
                'options' => array(
                    'ckeditor' => 'CKEditor',
                    'markitup' => 'MarkItUp',
                    'whizzywig' => 'Whizzywig',
                    //'tinymce' => 'TinyMCE' --> TODO
                )
            )
        );
    ?>
<?php echo $this->Html->useTag('fieldsetend'); ?>
