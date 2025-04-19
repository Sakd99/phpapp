<?php

// تشغيل الرابط الرمزي
shell_exec('php artisan storage:link');

echo "Storage link created successfully!";
