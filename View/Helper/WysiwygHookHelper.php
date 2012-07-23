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

			switch (Configure::read('Modules.Wysiwyg.settings.editor')){
				case 'ckeditor':
					default:
						$data['options']['class'] = "{$data['options']['class']} ckeditor";
				break;

				case 'markitup':
					$data['options']['class'] = "{$data['options']['class']} markitup";
				break;

				case 'nicedit':
					$data['options']['class'] = "{$data['options']['class']} nicedit";
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
			switch (Configure::read('Modules.Wysiwyg.settings.editor')) {
				case 'ckeditor':
					default:
						$js['file'][] = '/wysiwyg/js/ckeditor/ckeditor.js';
						$js['inline'][] = "$(document).ready(function () {
							CKEDITOR.config.entities = false;

							// Get a CKEDITOR.dialog.contentDefinition object by its ID.
							var getById = function(array, id, recurse) {
								for (var i = 0, item; (item = array[i]); i++) {
									if (item.id == id) return item;
										if (recurse && item[recurse]) {
											var retval = getById(item[recurse], id, recurse);
											if (retval) return retval;
										}
								}
								return null;
							};

							// modify existing Link dialog
							CKEDITOR.on( 'dialogDefinition', function( ev )	{
								// Overrides definition.
								var definition = ev.data.definition;
								definition.onFocus = CKEDITOR.tools.override(definition.onFocus, function(original) {
									return function() {
										original.call(this);
											if (this.getValueOf('info', 'linkType') == 'qaNode') {
												this.getContentElement('info', 'localPage_path').select();
											}
									};
								});

								// Overrides linkType definition.
								var infoTab = definition.getContents('info');
								var content = getById(infoTab.elements, 'linkType');

								content.items.unshift(['" . __t('Link to local content') . "', 'qaNode']);
								content['default'] = 'qaNode';
								infoTab.elements.push({
									type: 'vbox',
									id: 'localPageOptions',
									children: [{
										type: 'select',
										id: 'localPage_path',
										label: '" . __t('Select content') . ":',
										required: true,
										items: " . $this->json_nodes() .",
										setup: function(data) {
											if ( data.qaNode )
												this.setValue( data.qaNode );
										}
									}]
								});
								content.onChange = CKEDITOR.tools.override(content.onChange, function(original) {
									return function() {
										original.call(this);
										var dialog = this.getDialog();
										var element = dialog.getContentElement('info', 'localPageOptions').getElement().getParent().getParent();
										if (this.getValue() == 'qaNode') {
											element.show();
											if (editor.config.linkShowTargetTab) {
												dialog.showPage('target');
											}
											var uploadTab = dialog.definition.getContents('upload');
											if (uploadTab && !uploadTab.hidden) {
												dialog.hidePage('upload');
											}
										}
										else {
											element.hide();
										}
									};
								});
								content.setup = function(data) {
									if (!data.type || (data.type == 'url') && !data.url) {
										data.type = 'qaNode';
									}
									else if (data.url && !data.url.protocol && data.url.url) {
										if (path) {
											data.type = 'qaNode';
											data.localPage_path = path;
											delete data.url;
										}
									}
									this.setValue(data.type);
								};
								content.commit = function(data) {
									data.type = this.getValue();
									if (data.type == 'qaNode') {
										data.type = 'url';
										var dialog = this.getDialog();
										dialog.setValueOf('info', 'protocol', '');
										dialog.setValueOf('info', 'url', dialog.getValueOf('info', 'localPage_path'));
									}
								};
							});
						});";
				break;

				case 'markitup':
					$js['file'][] = '/wysiwyg/js/markitup/jquery.markitup.js';
					$js['file'][] = '/wysiwyg/js/markitup/sets/default/set.js';
					$js['inline'][] = "
						$(document).ready(function() {
							$('textarea.markitup').markItUp(mySettings);
						});
					";
				break;

				case 'nicedit':
					$js['file'][] = '/wysiwyg/js/nicedit/nicEdit.js';
					$js['inline'][] = "
						$(document).ready(function() {
							$('.nicedit').each(function() {
								new nicEditor({iconsPath : '" . Router::url('/wysiwyg/js/nicedit/nicEditorIcons.gif') . "'}).panelInstance($(this).attr('id'));
							});
						});
					";
				break;

				case 'tinymce':
					$js['file'][] = '/wysiwyg/javascript/get_file/tiny_mce/jquery.tinymce.js';
					$scriptURL = Router::url('/wysiwyg/javascript/get_file/tiny_mce/tiny_mce.js', true);
					$mediaManager = '';

					if (Configure::read('Modules.Mediamanager')) {
						$mediaManager = 'file_browser_callback : function(field_name, url, type, win) {';
						$mediaManager .= "var w = window.open('" . Router::url('/admin/mediamanager/connector/wysiwyg_browser/tinymce/', true) . "', null, 'width=600,height=500');";
						$mediaManager .= "w.tinymceFileField = field_name;";
						$mediaManager .= "w.tinymceFileWin = win;";
						$mediaManager .= "}, ";
					}

					$js['inline'][] = "
						$(document).ready(function() {
							$('textarea.tinymce').tinymce({
								// Location of TinyMCE script
								script_url : '{$scriptURL}',

								// General options
								theme : 'advanced',
								plugins : 'pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
								verify_html : false,
								cleanup_on_startup : false,
								apply_source_formatting : true,
								gecko_spellcheck : true,
								convert_urls : false,
								relative_urls : false,
								debug : false,
								strict_loading_mode : 1,
								{$mediaManager}

								// Theme options
								theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
								theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
								theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
								theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak',

								theme_advanced_toolbar_location : 'top',
								theme_advanced_toolbar_align : 'left',
								theme_advanced_statusbar_location : 'bottom',
								theme_advanced_resizing : true,
								theme_advanced_resize_horizontal : true
							});
						});
					";
				break;

				case 'whizzywig':
					$js['file'][] = 'http://unverse.net/whizzery/whizzywig.js';
					$js['inline'][] = "
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

	public function stylesheets_alter(&$css) {
		if (isset($this->_View->viewVars['wysiwygCount']) && $this->_View->viewVars['wysiwygCount'] > 0) {
			switch (Configure::read('Modules.Wysiwyg.settings.editor')) {
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

	public function json_nodes() {
		$Node = ClassRegistry::init('Node.Node');
		$o = array();
		$nodes = $Node->find('all',
			array(
				'fields' => array('title', 'slug', 'node_type_id'),
				'recursive' => -1,
				'order' => array('modified' => 'DESC')
				)
		);

		foreach ($nodes as $node) {
			$url = Router::url("/{$node['Node']['node_type_id']}/{$node['Node']['slug']}.html", true);
			$url = QuickApps::strip_language_prefix($url);
			$o[] = array("{$node['Node']['title']} ({$node['Node']['slug']})", $url);
		}

		return json_encode($o);
	}
}