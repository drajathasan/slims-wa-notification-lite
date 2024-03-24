<?php
use SLiMS\Config;
defined('INDEX_AUTH') or die('Direct access is not allowed!');

require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';

if (isset($_POST['saveData'])) {
    Config::create('whacenter', '<?php return [\'device_id\' => \'' . $_POST['device_id'] . '\']; ?>');
    toastr(__('ID Divais berhasil diperbaharui'))->success();
    exit(redirect()->simbioAJAX(pluginUrl(reset: true)));
}

?>
<div class="menuBox">
    <div class="menuBoxInner biblioIcon">
        <div class="per_title">
            <h2><?php echo __('Konfigurasi Divais Whacenter'); ?></h2>
        </div>
    </div>
</div>
<?php
// create new instance
$form = new simbio_form_table_AJAX('mainForm', pluginUrl(reset: true), 'post');
$form->submit_button_attr = 'name="saveData" value="' . __('Save') . '" class="s-btn btn btn-default"';
// form table attributes
$form->table_attr = 'id="dataList" cellpadding="0" cellspacing="0"';
$form->table_header_attr = 'class="alterCell"';
$form->table_content_attr = 'class="alterCell2"';

$form->addTextField('text', 'device_id', __('ID Divais Whacenter'), config('whacenter.device_id', ''), 'class="form-control" style="width: 50%;"');
echo $form->printOut();