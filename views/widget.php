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
    <tbody>
      <tr>
        <th scope="col" class="uploader_filename"><?=$this->text('label_filename')?></th>
        <th scope="col" class="uploader_size"><?=$this->text('label_size')?></th>
        <th scope="col" class="uploader_progress"><?=$this->text('label_state')?></th>
        <th scope="col" class="uploader_button"></th>
      </tr>
    </tbody>
    <template class="uploader_row_template">
      <tr class="uploader_row">
        <th scrope="row" class="uploader_filename"></th>
        <td class="uploader_size"></td>
        <td class="uploader_progress"><progress value="0"></progress></td>
        <td class="uploader_button">
          <button type="button" class="uploader_remove" title="<?=$this->text('label_remove')?>">
            <svg xmlns="http://www.w3.org/2000/svg" focusable="false" height="1.5em" viewBox="0 -960 960 960" width="1.5em" fill="currentColor"><path d="M200-440v-80h560v80H200Z"/></svg>
          </button>
        </td>
      </tr>
    </template>
  </table>
  <div class="uploader_dropzone"><?=$this->text("label_drop")?></div>
  <div class="uploader_buttons">
    <button type="button" class="uploader_pickfiles">
      <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" height="1.5em" viewBox="0 -960 960 960" width="1.5em" fill="currentColor"><path d="M440-440H200v-80h240v-240h80v240h240v80H520v240h-80v-240Z"/></svg>
      <span><?=$this->text('label_select_files')?><span>
    </button>
    <button type="button" class="uploader_uploadfiles">
      <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" height="1.5em" viewBox="0 -960 960 960" width="1.5em" fill="currentColor"><path d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg>
      <span><?=$this->text('label_upload_files')?></span>
    </button>
  </div>
</div>
