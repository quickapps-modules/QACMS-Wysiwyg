<?php
class JavascriptController extends WysiwygAppController {
	var $name = 'Javascript';
	var $uses = array();

    public function beforeFilter(){ 
        $this->Auth->allow('get_file');
        parent::beforeFilter();
    }

    // file must be a relative to wysiwyg/webroot/js/
    public function get_file() {
        $file = CakePlugin::path('Wysiwyg') . 'webroot' . DS . 'js' . DS . implode(DS, func_get_args());

        if (is_file($file)) {
            Configure::write('debug', 0);

            $info = pathinfo($file);

            if (in_array($info['extension'], array('jpeg', 'tiff', 'jpg', 'png', 'gif'))) {
                $info = pathinfo($file);
                $this->viewClass = 'Media';
                $params = array(
                    'id' => $this->__fileName($file) . ".{$info['extension']}",
                    'name' => $this->__fileName($file),
                    'download' => false,
                    'extension' => $info['extension'],
                    'path' => str_replace($this->__fileName($file) . ".{$info['extension']}", '', $file)
                );

                $this->set($params);
            } else {
                header('Content-type: '. $this->response->type($info['extension']));
                die(readfile($file));
            }
        } else {
            die('invalid file: ' . $file);
        }
    }

    public function __fileName($filepath){
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];
        // split the string by the literal dot in the filename
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
        // get the last dot position
        $lastdot = $pattern[count($pattern)-1][1];
        // now extract the filename using the basename function
        $filename = basename(substr($string, 0, $lastdot-1));
        // return the filename part
        return $filename;
    }   
}