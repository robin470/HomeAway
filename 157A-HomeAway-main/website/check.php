<?php
include "database.php";

if ($conn instanceof mysqli) {
    echo "Database connection is working!";
} else {
    echo "Database connection failed.";
}
