<?php namespace iscms\Alisms;

interface SendSmsApi
{
    public function send($phone,$name,$content,$code);
}