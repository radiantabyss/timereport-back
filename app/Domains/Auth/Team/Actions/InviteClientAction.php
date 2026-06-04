<?php
namespace App\Domains\Auth\Team\Actions;

use Illuminate\Routing\Controller as Action;
use RA\MailSender;
use RA\Response;
use RA\Auth\Services\ClassName;
use App\Models as Model;
use App\Domains\Auth\Team\Mail\InviteClientMail;
use App\Domains\Auth\Team\Validators\InviteClientValidator;
use App\Domains\Auth\Team\Transformers\InviteClientTransformer;

class InviteClientAction extends Action
{
    public function run() {
        $data = \Request::all();

        // validate
        $validation = InviteClientValidator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = InviteClientTransformer::run($data);
        $raw_password = $data['raw_password'];
        unset($data['raw_password']);

        $item = Model\User::create($data);

        // create default team
        $team = Model\Team::create([
            'created_by' => $item->id,
            'uuid' => \Str::uuid(),
            'name' => $data['name'],
        ]);

        // insert user in own team
        ClassName::Model('TeamMember')::create([
            'team_id' => $team->id,
            'user_id' => $item->id,
            'role' => 'owner',
        ]);

        MailSender::send(InviteClientMail::class, $item->email, [
            'email' => $item->email,
            'password' => $raw_password,
        ]);

        return Response::success();
    }
}
