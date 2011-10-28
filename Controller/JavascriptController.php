<?php
class JavascriptController extends WysiwygAppController {
	var $name = 'Javascript';
	var $uses = array();

    public function beforeFilter(){ 
        $this->Auth->allow('get_file');
        parent::beforeFilter();
    }

    # file must be a relative to wysiwyg/webroot/js/
    public function get_file() {
        $file = implode(DS, func_get_args());

        if ($file) {
            $file = urldecode($file);
            $file = preg_replace('/\/{2,}/', '',  "//{$file}//");
            $file = str_replace('/', DS, $file);
            $pos = strpos($file, '..');

            if ($pos === false) {
                if (is_file(CakePlugin::path('Wysiwyg') . 'webroot' . DS . 'js' . DS . $file)) {
                    $info = pathinfo(CakePlugin::path('Wysiwyg') . 'webroot' . DS . 'js' . DS . $file);

                    if (in_array($info['extension'], array('jpeg', 'tiff', 'jpg', 'png', 'gif'))) {
                        $fullPath = CakePlugin::path('Wysiwyg') . 'webroot' . DS . 'js' . DS . $file;
                        $info = pathinfo($fullPath);
                        $this->viewClass = 'Media';
                        $params = array(
                            'id' => $this->__fileName($fullPath) . ".{$info['extension']}",
                            'name' => $this->__fileName($fullPath),
                            'download' => false,
                            'extension' => $info['extension'],
                            'path' => str_replace($this->__fileName($fullPath) . ".{$info['extension']}", '', $fullPath)
                        );
                        
                        $this->set($params);
                    } else {
                        readfile(CakePlugin::path('Wysiwyg') . 'webroot' . DS . 'js' . DS . $file);
                        die;
                    }
                }
            } else {
                throw new NotFoundException(__t('File not found') );
            }
        } else {
            throw new NotFoundException(__t('File not found') );
        }
    }

    public function __fileName($filepath){
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];
        #split the string by the literal dot in the filename
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
        #get the last dot position
        $lastdot = $pattern[count($pattern)-1][1];
        #now extract the filename using the basename function
        $filename = basename(substr($string, 0, $lastdot-1));
        #return the filename part
        return $filename;
    }   
}