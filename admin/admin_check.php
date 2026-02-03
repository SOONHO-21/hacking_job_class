<?php
session_start();
if(!isset($_SESSION['userid']) || $_SESSION['userlevel'] !== 9) {
    echo "<script>alert('관리자 전용 페이지입니다.'); history.back();</script>";
    exit;
}