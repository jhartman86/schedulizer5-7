<?
defined('C5_EXECUTE') or die("Access Denied.");

$backgroundColor = '';
$image = false;
$baseFontSize = '';
$backgroundRepeat = 'no-repeat';
$textColor = '';
$linkColor = '';
$marginTop = '';
$marginLeft = '';
$marginRight = '';
$marginBottom = '';
$paddingTop = '';
$paddingLeft = '';
$paddingRight = '';
$paddingBottom = '';
$borderStyle = '';
$borderWidth = '';
$borderColor = '';
$borderRadius = '';
$alignment = '';
$rotate = '';
$boxShadowHorizontal = '';
$boxShadowVertical = '';
$boxShadowBlur = '';
$boxShadowSpread = '';
$boxShadowColor = '';
$customClass = '';
$sliderMin = \Config::get('concrete.limits.style_customizer.size_min', -50);
$sliderMax = \Config::get('concrete.limits.style_customizer.size_max', 200);
$set = $style->getStyleSet();
if (is_object($set)) {
    $backgroundColor = $set->getBackgroundColor();
    $textColor = $set->getTextColor();
    $linkColor = $set->getLinkColor();
    $image = $set->getBackgroundImageFileObject();
    $backgroundRepeat = $set->getBackgroundRepeat();
    $baseFontSize = $set->getBaseFontSize();
    $marginTop = $set->getMarginTop();
    $marginLeft = $set->getMarginLeft();
    $marginRight = $set->getMarginRight();
    $marginBottom = $set->getMarginBottom();
    $paddingTop = $set->getPaddingTop();
    $paddingLeft = $set->getPaddingLeft();
    $paddingRight = $set->getPaddingRight();
    $paddingBottom = $set->getPaddingBottom();
    $borderStyle = $set->getBorderStyle();
    $borderWidth = $set->getBorderWidth();
    $borderColor = $set->getBorderColor();
    $borderRadius = $set->getBorderRadius();
    $alignment = $set->getAlignment();
    $rotate = $set->getRotate();
    $boxShadowHorizontal = $set->getBoxShadowHorizontal();
    $boxShadowVertical = $set->getBoxShadowVertical();
    $boxShadowBlur = $set->getBoxShadowBlur();
    $boxShadowSpread = $set->getBoxShadowSpread();
    $boxShadowColor = $set->getBoxShadowColor();
    $customClass = $set->getCustomClass();
}

$repeatOptions = array(
    'no-repeat' => t('None'),
    'repeat-x' => t('Horizontal'),
    'repeat-y' => t('Vertical'),
    'repeat' => t('Tile')
);
$borderOptions = array(
    'none' => t('None'),
    'solid' => t('Solid'),
    'dotted' => t('Dotted'),
    'dashed' => t('Dashed'),
    'double' => t('Double'),
    'groove' => t('Groove'),
    'ridge' => t('Ridge'),
    'inset' => t('Inset'),
    'outset' => t('Outset')
);

$alignmentOptions = array(
    '' => t('None'),
    'left' => t('Left'),
    'center' => t('Center'),
    'right' => t('Right'),
);


$customClassesSelect = array();

if (is_array($customClasses)) {
    foreach($customClasses as $class) {
        $customClassesSelect[$class] = $class;
    }
}

if ($style instanceof \Concrete\Core\Block\CustomStyle) {
    $method = 'concreteBlockInlineStyleCustomizer';
} else {
    $method = 'concreteAreaInlineStyleCustomizer';
}

$al = new Concrete\Core\Application\Service\FileManager();
$form = Core::make('helper/form');
?>

<form method="post" action="<?=$saveAction?>" id="ccm-inline-design-form">
<ul class="ccm-inline-toolbar ccm-ui">
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown" title="<?=t('Text Size and Color')?>"><i class="fa fa-font"></i></a>

        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <div>
                <?=t('Text Color')?>
                <?=Loader::helper('form/color')->output('textColor', $textColor);?>
            </div>
            <hr />
            <div>
                <?=t('Link Color')?>
                <?=Loader::helper('form/color')->output('linkColor', $linkColor);?>
            </div>
            <hr />
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Base Font Size')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="baseFontSize" id="baseFontSize" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $baseFontSize ? $baseFontSize : '0px' ?>" <?php echo $baseFontSize ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <div class="ccm-inline-select-container">
                <?=t('Alignment')?>
                <?=$form->select('alignment', $alignmentOptions, $alignment);?>
            </div>

        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown" title="<?=t('Background Color and Image')?>"><i class="fa fa-image"></i></a>

        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Background')?></h3>
            <div>
                <?=t('Color')?>
                <?=Loader::helper('form/color')->output('backgroundColor', $backgroundColor);?>
            </div>
            <hr />
            <div>
                <?=t('Image')?>
                <?=$al->image('backgroundImageFileID', 'backgroundImageFileID', t('Choose Image'), $image);?>
            </div>
            <div class="ccm-inline-select-container">
                <?=t('Tile')?>
                <?=$form->select('backgroundRepeat', $repeatOptions, $backgroundRepeat);?>
            </div>
        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown" title="<?=t('Borders')?>"><i class="fa fa-square-o"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Border')?></h3>
            <div>
                <?=t('Color')?>
                <?=Loader::helper('form/color')->output('borderColor', $borderColor);?>
            </div>
            <hr />
            <div class="ccm-inline-select-container">
                <?=t('Style')?>
                <?=$form->select('borderStyle', $borderOptions, $borderStyle);?>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Width')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
               <span class="ccm-inline-style-slider-display-value">
                <input type="text" name="borderWidth" id="borderWidth" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $borderWidth ? $borderWidth : '0px' ?>" <?php echo $borderWidth ? '' : 'disabled' ?> autocomplete="off" />
            </span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Radius')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="borderRadius" id="borderRadius" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $borderRadius ? $borderRadius : '0px' ?>" <?php echo $borderRadius ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
        </div>
    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown" title="<?=t('Margin and Padding')?>"><i class="fa fa-arrows-h"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Padding')?></h3>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Top')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="paddingTop" id="paddingTop" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingTop ? $paddingTop : '0px' ?>" <?php echo $paddingTop ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Right')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="paddingRight" id="paddingRight" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingRight ? $paddingRight : '0px' ?>" <?php echo $paddingRight ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Bottom')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="paddingBottom" id="paddingBottom" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingBottom ? $paddingBottom : '0px' ?>" <?php echo $paddingBottom ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Left')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
               <span class="ccm-inline-style-slider-display-value">
                <input type="text" name="paddingLeft" id="paddingLeft" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $paddingLeft ? $paddingLeft : '0px' ?>" <?php echo $paddingLeft ? '' : 'disabled' ?> autocomplete="off" />
            </span>
            </div>

            <? if ($style instanceof \Concrete\Core\Block\CustomStyle) { ?>
                <hr />
                <h3><?=t('Margin')?></h3>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Top')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                    <span class="ccm-inline-style-slider-display-value">
                        <input type="text" name="marginTop" id="marginTop" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $marginTop ? $marginTop : '0px' ?>" <?php echo $marginTop ? '' : 'disabled' ?> autocomplete="off" />
                    </span>
                </div>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Right')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                    <span class="ccm-inline-style-slider-display-value">
                        <input type="text" name="marginRight" id="marginRight" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $marginRight ? $marginRight : '0px' ?>" <?php echo $marginRight ? '' : 'disabled' ?> autocomplete="off" />
                    </span>
                </div>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Bottom')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                    <span class="ccm-inline-style-slider-display-value">
                        <input type="text" name="marginBottom" id="marginBottom" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $marginBottom ? $marginBottom : '0px' ?>" <?php echo $marginBottom ? '' : 'disabled' ?> autocomplete="off" />
                    </span>
                </div>
                <div>
                    <span class="ccm-inline-style-slider-heading"><?=t('Left')?></span>
                    <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                    <span class="ccm-inline-style-slider-display-value">
                        <input type="text" name="marginLeft" id="marginLeft" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $marginLeft ? $marginLeft : '0px' ?>" <?php echo $marginLeft ? '' : 'disabled' ?> autocomplete="off" />
                    </span>
                </div>

            <? } ?>
        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown" title="<?=t('Shadow and Rotation (CSS3)')?>"><i class="fa fa-magic"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Shadow')?></h3>
            <div>
                <?=t('Color')?>
                <?=Loader::helper('form/color')->output('boxShadowColor', $boxShadowColor);?>
            </div>
            <hr />
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Horizontal Position')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="boxShadowHorizontal" id="boxShadowHorizontal" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $boxShadowHorizontal ? $boxShadowHorizontal : '0px' ?>" <?php echo $boxShadowHorizontal ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Vertical Position')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="boxShadowVertical" id="boxShadowVertical" data-value-format="px" class="ccm-inline-style-slider-value" value="<?php echo $boxShadowVertical ? $boxShadowVertical : '0px' ?>" <?php echo $boxShadowVertical ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Blur')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="boxShadowBlur" id="boxShadowBlur" class="ccm-inline-style-slider-value" data-value-format="px" value="<?php echo $boxShadowBlur ? $boxShadowBlur : '0px' ?>" <?php echo $boxShadowBlur ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Spread')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
                <span class="ccm-inline-style-slider-display-value">
                    <input type="text" name="boxShadowSpread" id="boxShadowSpread" class="ccm-inline-style-slider-value" data-value-format="px" value="<?php echo $boxShadowSpread ? $boxShadowSpread : '0px' ?>" <?php echo $boxShadowSpread ? '' : 'disabled' ?> autocomplete="off" />
                </span>
            </div>
            <hr/>
            <h3><?=t('Rotate')?></h3>
            <div>
                <span class="ccm-inline-style-slider-heading"><?=t('Rotation (in degrees)')?></span>
                <div class="ccm-inline-style-sliders" data-style-slider-min="<?= $sliderMin ?>" data-style-slider-max="<?= $sliderMax ?>" data-style-slider-default-setting="0"></div>
               <span class="ccm-inline-style-slider-display-value">
                <input type="text" name="rotate" id="rotate" class="ccm-inline-style-slider-value ccm-slider-value-unit-appended" data-value-format="" value="<?php echo $rotate ? $rotate : '0' ?>" autocomplete="off" /> &deg;
            </span>
            </div>

        </div>

    </li>
    <li class="ccm-inline-toolbar-icon-cell"><a href="#" data-toggle="dropdown" title="<?=t('Custom CSS Classes, Block Name, Custom Templates and Reset Styles')?>"><i class="fa fa-cog"></i></a>
        <div class="ccm-inline-design-dropdown-menu dropdown-menu">
            <h3><?=t('Advanced')?></h3>

            <div>
                <?=t('Custom Class')?>
                <?= $form->text('customClass', $customClass);?>
            </div>
            <hr/>

            <? if ($style instanceof \Concrete\Core\Block\CustomStyle && $canEditCustomTemplate) { ?>
                <div class="ccm-inline-select-container">
                    <?=t('Custom Template')?>
                    <select id="bFilename" name="bFilename" class="form-control">
                        <option value="">(<?=t('None selected')?>)</option>
                        <?
                        foreach($templates as $tpl) {
                            ?><option value="<?=$tpl->getTemplateFileFilename()?>" <? if ($bFilename == $tpl->getTemplateFileFilename()) { ?> selected <? } ?>><?=$tpl->getTemplateFileDisplayName()?></option><?
                        }
                        ?>
                    </select>
                 </div>
                <hr/>

            <? } ?>
            <div>
                <button data-reset-action="<?=$resetAction?>" data-action="reset-design" type="button" class="btn-block btn btn-danger"><?=t("Clear Styles")?></button>
            </div>
        </div>
    </li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
        <button data-action="cancel-design" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
    </li>
    <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
        <button data-action="save-design" class="btn btn-primary" type="button"><?=t('Save')?></button>
    </li>
</ul>
</form>

<script type="text/javascript">
    $('#ccm-inline-design-form').<?=$method?>();
    $("#customClass").select2({tags:<?= json_encode(array_values($customClassesSelect)); ?>, separator: " "});
</script>