<div id="<?=$this->anchor()?>">
    <div class="uploader_controls">
        <?=$this->typeSelect()?>
        <?=$this->subdirSelect()?>
        <?=$this->resizeSelect()?>
    </div>
    <iframe src="<?=$this->iframeSrc()?>" frameBorder="0" class="uploader"></iframe>
</div>
