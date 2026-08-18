<?php
$dir = __DIR__.'/storage/app/backups';
echo "Scandir:\n";
print_r(scandir($dir));
