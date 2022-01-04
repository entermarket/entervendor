<?php

namespace App\Http\Controllers;




use App\Models\User;

use Illuminate\Http\Request;
use Notification;

class NotificationController extends Controller
{

  public $user;
  public $admin;

  public function __construct()
  {

    $this->user = auth('api')->user();
    $this->admin = auth('admin_api')->user();
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
    return response()->json('cleared');
  }

  public function admingetnotifications()
  {

    return $this->admin->notifications;
  }
  public function adminunreadnotifications()
  {

    return $this->admin->unreadnotifications;
  }

  public function adminmarkreadnotifications()
  {

    $this->admin->unreadNotifications->markAsRead();
    return  $this->admin->notifications;
  }

  public function adminmarksinglenotification($id)
  {

    $this->admin->unreadNotifications->where('id', $id)->markAsRead();
    return $this->admin->notifications;
  }


  public function admindestroy()
  {
    $this->admin->notifications()->delete();
    return response()->json('cleared');
  }
}
