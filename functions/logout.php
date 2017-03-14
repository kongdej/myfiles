<?php
if(isset($_SESSION['views']))
  unset($_SESSION['views']);
session_destroy();
logging('Logout');