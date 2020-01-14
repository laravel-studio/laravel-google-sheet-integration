<?php

namespace laravelstudio\laravelgooglesheetintegration\models;

use Illuminate\Database\Eloquent\Model;

class userGoogleSheet extends Model
{
  protected $table = 'user_google_sheets';
  protected $guarded =[];

  public function user()
  {
      return $this->belongsTo('laravelstudio\laravelgooglesheetintegration\models\googleSheetUser','id');
  }
}
