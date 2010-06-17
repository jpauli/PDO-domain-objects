<?php
require __DIR__ .'/../vendor/autoload.php';

class Messages extends JPDO\Result\SavableObjects { }

$p = new JPDO\PDO("sqlite:".__DIR__."/base.sq3");
$r = $p->query('SELECT * FROM messages');
$o = $r->fetchObjectOfClass("Messages");

$o->message = "foobar";
$o->id = 88;
Messages::setPk('id');

$o->save();
