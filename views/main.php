<?php

use Plib\View;

/**
 * @var View $this
 * @var string $serial
 * @var string $plupload
 * @var string $uploader
 */
?>

<script type="module" src="<?=$this->esc($plupload)?>"></script>
<script type="module" src="<?=$this->esc($uploader)?>"></script>
<div class="uploader_placeholder" data-serial="<?=$this->esc($serial)?>"></div>
