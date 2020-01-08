<?php

namespace itobuz\laravelgooglesheetintegration\models;

use Illuminate\Database\Eloquent\Model;

class userGoogleSheet extends Model
{
  protected $table = 'user_google_sheets';
  protected $guarded =[];

  public function user()
  {
      return $this->belongsTo('itobuz\laravelgooglesheetintegration\models\googleSheetUser','id');
  }
}
