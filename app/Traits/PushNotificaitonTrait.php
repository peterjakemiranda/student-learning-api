<?php

namespace App\Traits;

use App\User;
use App\Activity;
use App\Announcement;
use App\Notification;
use App\Quiz;

trait PushNotificaitonTrait
{
    public function sendAnnouncementNotification(Announcement $announcement)
    {
        $students = $this->getStudentByCourse($announcement->course_id);

          $this->sendPushNotification(
              $students, 
              "New announcement for {$announcement->course->title}",
              $announcement->body,
              'announcement',
              ['course_id' => $announcement->course_id, 'announcement_id' => $announcement->id],
          );
    }

    public function sendQuizStartedNotification(Quiz $quiz)
    {
        $students = $this->getStudentByCourse($quiz->course_id);

          $this->sendPushNotification(
              $students, 
              'A quiz has been started!',
              "{$quiz->title} has been started on {$quiz->course->title}",
              'quiz',
              ['course_id' => $quiz->course_id, 'activity_id' => $quiz->id],
          );
    }

    public function sendQuizStoppedNotification(Quiz $quiz)
    {
        $students = $this->getStudentByCourse($quiz->course_id);

          $this->sendPushNotification(
              $students, 
              'A quiz has been stopped!',
              "{$quiz->title} has been stopped on {$quiz->course->title}, you can no longer submit answers.",
              'quiz',
              ['course_id' => $quiz->course_id, 'activity_id' => $quiz->id],
          );
    }

    public function sendActivityAddedNotification(Activity $activity)
    {
        $students = $this->getStudentByCourse($activity->course_id);

          $this->sendPushNotification(
              $students, 
              'You have a new activity!',
              "{$activity->title} has been added to {$activity->course->title}",
              'activity',
              ['course_id' => $activity->course_id, 'activity_id' => $activity->id],
          );
    }

    public function getStudentByCourse($courseId)
    {
      return User::whereHas('courses',function($query) use ($courseId) {
                $query->where('id', $courseId);
            })
            ->where('role', 'student')
            ->whereNotNull('device_key')
            ->select(['id', 'device_key'])
            ->get();
    }

    public function sendPushNotification($users = null, $title = '', $body = '', $type = '', $data = [])
    {
      try {
        $userDevices = [];
        $userIds = [];
        foreach( $users as $user) {
          $userDevices[] = $user->device_key;
          $userIds[] = $user->id;
        }
        $this->saveNotificationToDb($userIds, $title, $body, $type, $data);

        $data['type'] = $type;

        $notification = [
          'title' => $title,
          'body' => $body,
          'click_action' => 'FCM_PLUGIN_ACTIVITY',
        ];
        $notification = array_filter($notification, function ($value) {
            return null !== $value;
        });
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = [
          'registration_ids' => $userDevices,
          'notification' => $notification,
          'data' => $data,
        ];

        $fields = json_encode($fields);

        $headers = [
          'Authorization: key=' . config('services.fcm.server_key'),
          'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
      } catch(\Throwable $e) {
        info('exception', compact('e'));
      }
    }

    public function saveNotificationToDb($userIds, $title, $body, $type, $data = [])
    {
      foreach ($userIds as $userId) {
        Notification::create([
          'user_id' => $userId,
          'title' => $title,
          'body' => $body,
          'type' => $type,
          'data' => $data,
        ]);
      } 
    }
}
