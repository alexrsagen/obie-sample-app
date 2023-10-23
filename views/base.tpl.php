<?php
/** @var \Obie\View $this */
$this->begin();
?>
<html lang="<?= $this->get('language') ?>">
	<?= $this->block('content') ?>
	<script src="app.js" nonce="<?= $this->get('nonce') ?>"></script>
</html>
<?php $this->end(); ?>