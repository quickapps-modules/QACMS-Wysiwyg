<?php
/**
 * Wysiwyg Hooks Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <y2k2000@gmail.com>
 * @link     http://www.quickapps.es
 */
class WysiwygHookHelper extends AppHelper {
	
    public function form_textarea_alter(&$data) {
        if (isset($data['options']['type']) &&  $data['options']['type'] == 'textarea') {
            if (!isset($data['options']['class']) || strpos('full', $data['options']['class']) === false) {
                return;
            }

            switch (Configure::read('Modules.wysiwyg.settings.editor')){
                case 'ckeditor': 
                    default:
                        $data['options']['class'] = "{$data['options']['class']} ckeditor";
                break;

                case 'markitup':
                    $data['options']['class'] = "{$data['options']['class']} markitup";
                break;

                case 'tinymce':
                    $data['options']['class'] = "{$data['options']['class']} tinymce";
                break;

                case 'whizzywig':
                    $data['options']['class'] = "{$data['options']['class']} whizzywig";
                    $data['options']['style'] = 'width:100%; height:400px;';
                break;
            }

            if (isset($data['options']['required'])) {
                unset($data['options']['required']);
            }

            $this->_View->viewVars['wysiwygCount'] = @intval($this->_View->viewVars['wysiwygCount']) + 1;
        }
    }

    public function javascripts_alter(&$js) {
        if (isset($this->_View->viewVars['wysiwygCount']) && $this->_View->viewVars['wysiwygCount'] > 0) {
            switch (Configure::read('Modules.wysiwyg.settings.editor')) {
                case 'ckeditor': 
                    default:
                        $js['file'][] = '/wysiwyg/js/ckeditor/ckeditor.js';
                break;

                case 'markitup':
                    $js['file'][] = '/wysiwyg/js/markitup/jquery.markitup.js';
                    $js['file'][] = '/wysiwyg/js/markitup/sets/default/set.js';
                    $scriptURL = $this->_View->Html->url('/wysiwyg/javascript/get_file/?file=tiny_mce/tiny_mce.js', true);
                    $js['embed'][] ="
                        $().ready(function() {
                            $('textarea.markitup').markItUp(mySettings);
                        });
                    ";
                break;

                case 'tinymce':
                    $js['file'][] = '/wysiwyg/javascript/get_file/tiny_mce/jquery.tinymce.js';
                    $scriptURL = $this->_View->Html->url('/wysiwyg/javascript/get_file/tiny_mce/tiny_mce.js');
                    $js['embed'][] = "
                        $().ready(function() {
                            $('textarea.tinymce').tinymce({
                                // Location of TinyMCE script
                                script_url : '{$scriptURL}',

                                // General options
                                theme : 'advanced',
                                plugins : 'searchreplace,contextmenu,paste,xhtmlxtras,media',
                                verify_html : false,
                                cleanup_on_startup : false,
                                apply_source_formatting : true,
                                gecko_spellcheck : true,
                                convert_urls : false,
                                relative_urls : false,
                                debug : true,
                                strict_loading_mode : 1,
                                
                                // Theme options
                                theme_advanced_buttons1 : 'undo,redo,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,fontselect,fontsizeselect',
                                theme_advanced_buttons2 : 'link,unlink,image,|,code,|,media,|,forecolor,backcolor,|,charmap',
                                theme_advanced_buttons3 : '',
                                theme_advanced_toolbar_location : 'top',
                                theme_advanced_toolbar_align : 'left',
                                theme_advanced_resizing : true,
                                theme_advanced_resize_horizontal : true                                
                            });
                        });
                    ";
                break;

                case 'whizzywig':
                    $js['file'][] = 'http://unverse.net/whizzery/whizzywig.js';
                    $js['embed'][] = "
                        $().ready(function() {
                            $('textarea.whizzywig').each(function(){
                                makeWhizzyWig(this.id);
                            });
                        });
                    ";
                break;       
            }
        }
    }

    public function stylesheets_alter($css) { 
        if (isset($this->_View->viewVars['wysiwygCount']) && $this->_View->viewVars['wysiwygCount'] > 0) {
            switch (Configure::read('Modules.wysiwyg.settings.editor')) {
                case 'markitup':
                    $css['all'][] = '/wysiwyg/js/markitup/skins/simple/style.css';
                    $css['all'][] = '/wysiwyg/js/markitup/sets/default/style.css';
                break;

                case 'tinymce':
                break;

                case 'whizzywig':
                break;
            }
        }
    }
}