<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var mixed $pluploadConfig
 * @var string $typeSelectChangeUrl
 * @var string $subdirSelectChangeUrl
 * @var string $resizeSelectChangeUrl
 */
?>

<div class="uploader_widget" data-config='<?=$this->json($pluploadConfig)?>'>
  <div class="uploader_controls">
<?if (!empty($typeOptions)):?>
    <select class="uploader_type" title="<?=$this->text('label_type')?>" data-url="<?=$this->esc($typeSelectChangeUrl)?>">
<?  foreach ($typeOptions as $type => $selected):?>
      <option value="<?=$this->esc($type)?>" <?=$this->esc($selected)?>><?=$this->esc($type)?></option>
<?  endforeach?>
    </select>
<?endif?>
<?if (!empty($subdirOptions)):?>
    <select class="uploader_subdir" title="<?=$this->text('label_subdir')?>" data-url="<?=$this->esc($subdirSelectChangeUrl)?>">
<?  foreach ($subdirOptions as $subdir => $selected):?>
      <option value="<?=$this->esc($subdir)?>" <?=$this->esc($selected)?>><?=$this->esc($subdir)?></option>
<?  endforeach?>
    </select>
<?endif?>
<?if (!empty($resizeOptions)):?>
    <select class="uploader_resize" title="<?=$this->text('label_resize')?>" data-url="<?=$this->esc($resizeSelectChangeUrl)?>">
<?  foreach ($resizeOptions as $size => $selected):?>
      <option value="<?=$this->esc($size)?>" <?=$this->esc($selected)?>><?=$this->esc($size)?></option>
<?  endforeach?>
    </select>
<?endif?>
  </div>
  <table class="uploader_filelist">
    <tr>
      <th class="uploader_filename"><?=$this->text('label_filename')?></th>
      <th class="uploader_size"><?=$this->text('label_size')?></th>
      <th class="uploader_progress"><?=$this->text('label_state')?></th>
      <th></th>
    </tr>
    <tr class="uploader_row_template">
      <td class="uploader_filename"></td>
      <td class="uploader_size"></td>
      <td class="uploader_progress"></td>
      <td><button class="uploader_remove"><?=$this->text('label_remove')?></button></td>
    </tr>
  </table>
  <div class="uploader_buttons">
    <button class="uploader_pickfiles"><?=$this->text('label_select_files')?></button>
    <button class="uploader_uploadfiles"><?=$this->text('label_upload_files')?></button>
  </div>
</div>
