<?php
namespace App\Domains\Auth\Team\Mail;

use RA\Mail;

class InviteClientMail extends Mail
{
    public function build() {
        $this->subject(__('Your '.config('app.name').' client account'));
        $this->view('Auth.Team::invite-client', $this->params);
        return $this;
    }
}
