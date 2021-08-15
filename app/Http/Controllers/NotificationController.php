<?php

namespace App\Http\Controllers;




use App\Models\User;

use Illuminate\Http\Request;
use Notification;

class NotificationController extends Controller
{

  public $user;

  public function __construct()
  {

    $this->user = auth('api')->user();
  }

  public function getnotifications()
  {

    return $this->user->notifications;
  }
  public function unreadnotifications()
  {

    return $this->user->unreadnotifications;
  }

  public function markreadnotifications()
  {

    $this->user->unreadNotifications->markAsRead();
    return  $this->user->notifications;
  }

  public function marksinglenotification($id)
  {

    $this->user->unreadNotifications->where('id', $id)->markAsRead();
    return $this->user->notifications;
  }


  public function destroy()
  {
    $this->user->notifications()->delete();
  }
}
