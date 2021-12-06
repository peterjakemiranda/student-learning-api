<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Course;

class StudentAddedToCourse extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User The user instance.
     */
    protected $user;
    protected $teacher;
    protected $course;

    /**
     * Create a new message instance.
     *
     * @param User $user The user instance.
     */
    public function __construct(User $user, User $teacher, Course $course)
    {
        $this->user = $user;
        $this->teacher = $teacher;
        $this->course = $course;
    }

    /**
     * Build the message.
     *
     * @return $this The email message.
     */
    public function build()
    {
        return $this->to($this->user->email)
            ->subject('You have been invited to participate in a class on SDSSU Learning App')
            ->markdown('emails.studentAddedOnClass')
            ->with([
                'user' => $this->user,
                'teacher' => $this->teacher,
                'course' => $this->course,
            ]);
    }
}
