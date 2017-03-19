<?php
/**
 * Created by PhpStorm.
 * User: minos
 * Date: 16.02.2017
 * Time: 22:35
 */

namespace App\Admin\Model;


class BaseModel
{
    const
        //user talbe
        USER_TABLE_NAME='flack_user',
        USER_NAME='name',
        USER_SURNAME='surname',
        USER_ID='id_user',
        //document table
        DOCUMENT_NAME_TABLE='flack_document',
        DOCUMENT_SEO_URL='seo_url',
        DOCUMENT_TITLE='title',
        DOCUMENT_CONTENTS='contents',
        DOCUMENT_CREATE_DATE_TIME='create_date_time',
        DOCUMENT_TAGS='tags',
        DOCUMENT_PRIORITY='priority',
        DOCUMENT_ID="id_document",
        DOCUMENT_UPDATE="update_date_time",
        DOCUMENT_TIMER="timer_id",
        //timer table
        TIMER_NAME_TABLE='flack_timer',
        TIMER_DATE_TIME_UPDATE='date_time_update',
        TIMER_DATE_TIME_START='date_time_start',
        TIMER_ID='id_timer',
        TIMER_DATE_TIME_END='date_time_end';


}