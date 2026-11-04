<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReportManager extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->array['view'])
            ->from($this->array['from'], env('MAIL_FROM_NAME'))
            ->subject($this->array['subject'])
            ->with([
                'averge_order_value' => $this->array['averge_order_value'],
                'monthtodateitemsold' => $this->array['monthtodateitemsold'],
                'monthtodatetotalorder' => $this->array['monthtodatetotalorder_1'],
                'monthtodatetotalsales' => $this->array['monthtodatetotalsales'],
                'totalitemunitsold' => $this->array['totalitemunitsold'],
                'totalorders' => $this->array['totalorder_1'],
                'salesstats' => $this->array['salesstats'],
                'dailytotalsales' => $this->array['dailytotalsales'],
            ])
            ->attach($this->array['file'], [
                'as' => $this->array['file_name'],
                'mime' => 'application/excel'
            ]);
    }
}