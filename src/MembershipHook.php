<?php
namespace SLiMS\Wa\Notification;

class MembershipHook extends BaseHook {
    public function afterSave(array $data)
    {
        toastr('Tungggu sebentar, sedang mepersiapkan notifikasi')->info();
        ob_end_flush();
        ob_flush();
        flush();

        $content = parseMustache(loadTemplate('newregismember'), $data[0]);

        $this->send(
            $data[0],
            $content
        );

        toastr('Notifikasi berhasil dikirim')->success();
        ob_end_flush();
        ob_flush();
        flush();
    }
}