<?php
require_once 'db.php';

session_destroy();
jsonResponse(true, null, '退出成功');
