<?php
use SLiMS\Config;
defined('INDEX_AUTH') or die('Direct access is not allowed!');

require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';

if (isset($_POST['saveData'])) {

    if (isset($_POST['filename'])) {
        $info = pathinfo($_POST['filename']);
        $_SESSION['filepath'] = WA_BASE_DIR . 'templates' . DS . $info['filename'] . '.pt';
    }

    if (!isset($_SESSION['filepath'])) {
        toastr(__('Data path tidak tersedia'))->error();
        exit(redirect()->simbioAJAX(pluginUrl(reset: true)));
    }

    $update = file_put_contents($_SESSION['filepath'], $_POST['content']);

    if ($update) toastr(__('Berhasil memperbaharui template'))->success();
    else toastr(__('Gagal menyimpan template'))->error();

    exit(redirect()->simbioAJAX(pluginUrl(reset: true)));
}

?>
<div class="menuBox">
    <div class="menuBoxInner biblioIcon">
        <div class="per_title">
            <h2><?php echo __('Template Pesan'); ?></h2>
        </div>
        <div class="infoBox">
            <p>Dibawah ini merupakan template yang digunakan oleh SLiMS dalam proses pengiriman notifikasi sesuai dengan tugas nya <a href="<?= pluginUrl(['file' => 'new']) ?>" class="btn btn-secondary">Tambah Baru</a></p>
        </div>
    </div>
</div>
<?php if (!isset($_GET['file'])): ?>
<div class="mx-3">
    <div class="row row-cols-1 row-cols-md-3">
        <?php foreach(array_diff(scandir(WA_BASE_DIR . 'templates'), ['.','..']) as $item): ?>
        <div class="col mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= ucwords($item) ?></h5>
                    <p class="card-text">Anda dapat mengedit template ini dengan preferensi anda dalam ekstensi .pt dengan format <em>mustache</em> sebagai teks dinamis.</p>
                    <a class="btn btn-primary" href="<?= pluginUrl(['action' => 'edit', 'file' => $item]) ?>"><?= __('Edit') ?></a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php 
else: 
    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', pluginUrl(reset: true), 'post');
    $form->submit_button_attr = 'name="saveData" value="' . __('Save') . '" class="s-btn btn btn-default"';
    // form table attributes
    $form->table_attr = 'id="dataList" cellpadding="0" cellspacing="0"';
    $form->table_header_attr = 'class="alterCell"';
    $form->table_content_attr = 'class="alterCell2"';

    $isExists = file_exists($path = WA_BASE_DIR . 'templates' . DS . basename($_GET['file']));
    if ($isExists) $_SESSION['filepath'] = $path;
    $content = $isExists ? file_get_contents($path) : '';

    if (basename($path) == 'new') $form->addTextField('text', 'filename', 'Filename', '', 'class="form-control col-2"');
    $form->addTextField('textarea', 'content', __('Content'), $content, 'class="form-control" style="height: 285px;"');
    echo $form->printOut();
endif;
