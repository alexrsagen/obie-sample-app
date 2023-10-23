<?php
use ObieSampleApp\App;
/** @var \Obie\View $this */
$this->extends('base');
$this->begin();
?>
<p>Hello world!</p>
<p><?= App::getI18n()->translate('Test translation') ?></p>
<?php $this->endBlock('content'); ?>