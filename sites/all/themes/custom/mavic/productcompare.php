<div style="position:relative">
    <div id="productCompareContainer"  >
        <div id="productCompareHeader" >
            <div id="nextprevmacro">
                <div class="backtolist"><a href="javascript: void(0)" onclick="closeProductCompare();"><?php echo t('complete range'); ?></a></div>
            </div>
        </div>
        <div id="loader" style="text-align:center"><?php echo t('Loading...'); ?><br /><img src="/<?php echo drupal_get_path('theme', 'mavic')?>/images/mavic-loader.gif" /></div>
        <table id="productCompareTable" border="0" cellspacing="12" cellpadding="0">
        </table>
    </div>
</div>