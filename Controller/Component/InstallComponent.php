<?php
class InstallComponent extends Component {
    public function beforeInstall() {
        return true;
    }

    public function afterInstall() {
        return true;
    }

    public function beforeUninstall() {
        return true;
    }

    public function afterUninstall() {
        return true;
    }
}