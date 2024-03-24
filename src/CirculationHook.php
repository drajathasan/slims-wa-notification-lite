<?php
namespace SLiMS\Wa\Notification;

use SLiMS\DB;

class CirculationHook extends BaseHook {
    private array $message = [];
    private array $data = [];

    public function afterTransaction(array $data)
    {
        $this->data = $data;

        if (isset($this->data['loan']) || isset($this->data['return']) || isset($this->data['extend'])) {
            toastr('Tungggu sebentar, sedang mepersiapkan notifikasi')->info();
            ob_end_flush();
            ob_flush();
            flush();
            $this
                ->setLoanReceipt()
                ->setReturnReceipt()
                ->setExtendReceipt();

            // set close separator
            $this->message[] = '_____________________';
            $this->message[] = 'Harap simpan resi ini sebagai bukti transaksi.';

            // member data
            $member = DB::query('select `member_phone` from `member` where `member_id` = ?', [$this->data['memberID']]);
            $memberData = $member->first();

            if ($member->count() < 1) {
                writeLog('system', $this->data['memberID'], 'circulation_hook', 'Member data is not exists');
                return;
            }

            // set to NSQ
            $dataToParse = [
                'member_name' => $this->data['memberName'],
                'detail' => implode("\n", $this->message),
                'member_id' => $this->data['memberID'],
                'member_name' => $this->data['memberName'],
                'member_type_name' => $this->data['memberType'],
                'date' => $this->data['date'],
                'library_name' => config('library_name'),
                'random_id' => md5(date('this')),
                'member_phone' => $memberData['member_phone']
            ];

            $this->send(
                $dataToParse,
                parseMustache(loadTemplate('Circulationreceipt'), $dataToParse)
            );

            toastr('Notifikasi berhasil dikirim')->success();
            ob_end_flush();
            ob_flush();
            flush();
        }
    }

    private function setLoanReceipt()
    {
        if (isset($this->data['loan'])) {

            $this->message[] = '=====================';
            $this->message[] = __('Loan');
            $this->message[] = '=====================';

            foreach ($this->data['loan'] as $key => $value) {

                $this->message[] = '';
                $this->message[] = '*' . $value['itemCode'] . '*';
                $this->message[] = '_' . $value['title'] . '_';
                $this->message[] = 'Tanggal Pinjam: ' . date('d-m-Y', strtotime($value['loanDate']));
                $this->message[] = 'Batas Pinjam: ' . date('d-m-Y', strtotime($value['dueDate']));
                $this->message[] = '';

            }
        }
        return $this;
    }

    private function setReturnReceipt()
    {
        if (isset($this->data['return'])) {
            
            $header = [];
            $header[] = '=====================';
            $header[] = __('Return');
            $header[] = '=====================';

            $message = [];
            foreach ($this->data['return'] as $key => $value) {

                if (isset($this->data['extend'])) {
                    $inExtend = false;
                    foreach ($this->data['extend'] as $ext_key => $ext_value) {
                        if (trim($ext_value['itemCode']) == trim($value['itemCode'])) {
                            $inExtend = true;
                            break;
                        }
                    }

                    if ($inExtend) {
                        unset($inExtend);
                        continue;
                    }
                }

                $message[] = '';
                $message[] = '*' . $value['itemCode'] . '*';
                $message[] = '_' . $value['title'] . '_';
                $message[] = 'Tanggal Kembali: ' . date('d-m-Y', strtotime($value['returnDate']));
                
                if (isset($value['overdues']) && $value['overdues']) {
                    if(is_array($value['overdues'])) {
                        $overdue = currency($value['overdues']['value']);
                    } else {
                        $overdue = $value['overdues'];
                    }
                    $message[] = 'Denda: ' . $overdue;
                }

                $message[] = '';
            }

            if (count($message) > 0) $this->message = array_merge($header, $message);
        }
        return $this;
    }

    private function setExtendReceipt()
    {
        if (isset($this->data['extend'])) {

            $this->message[] = '=====================';
            $this->message[] = __('Extend');
            $this->message[] = '=====================';

            foreach ($this->data['extend'] as $key => $value) {

                $this->message[] = '';
                $this->message[] = '*' . $value['itemCode'] . '*';
                $this->message[] = '_' . $value['title'] . '_';
                $this->message[] = 'Tanggal Pinjam: ' . date('d-m-Y', strtotime($value['loanDate']));
                $this->message[] = 'Batas Pinjam: ' . date('d-m-Y', strtotime($value['dueDate']));
                $this->message[] = '';

            }
        }
        return $this;
    }
}