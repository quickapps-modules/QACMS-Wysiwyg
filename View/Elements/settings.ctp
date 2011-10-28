<?php echo $this->Form->create('Module'); ?>
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
                        //'tinymce' => 'TinyMCE'
                    )
                )
            );
        ?>
    <?php echo $this->Html->useTag('fieldsetend'); ?>
   <?php echo $this->Form->input('Module.name', array('type' => 'hidden') ); ?>
    <!-- Submit -->
    <?php echo $this->Form->input(__t('Save all'), array('type' => 'submit') ); ?>
<?php echo $this->Form->end(); ?>