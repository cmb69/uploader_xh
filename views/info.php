<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $logo
 * @var string $version
 * @var list<array{class:string,label:string,stateLabel:string}> $checks
 */
?>

<h1>Uploader – <?=$this->esc($version)?></h1>
<div>
  <h2><?=$this->text('syscheck_title')?></h2>
<?foreach ($checks as $check):?>
  <p class="<?=$this->esc($check['class'])?>"><?=$this->text('syscheck_message', $check['label'], $check['stateLabel'])?></p>
<?endforeach?>
</div>
