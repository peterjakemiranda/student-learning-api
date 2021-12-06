<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Course;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User The user instance.
     */
    protected $user;
    protected $random_password;

    /**
     * Create a new message instance.
     *
     * @param User $user The user instance.
     */
    public function __construct(User $user, $random_password)
    {
        $this->user = $user;
        $this->random_password = $random_password;
    }

    /**
     * Build the message.
     *
     * @return $this The email message.
     */
    public function build()
    {
        return $this->to($this->user->email)
            ->subject('An account on SDSSU Learning App has been created for you')
            ->markdown('emails.userCreated')
            ->with([
                'user' => $this->user,
                'random_password' => $this->random_password,
            ]);
    }
}
