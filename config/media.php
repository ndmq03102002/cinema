<?php

use App\Enums\Constant;

return [
    'allowed_mime_types' => env(
        'MEDIA_ALLOWED_MIME_TYPES',
        'jpg,jpeg,png,gif,txt,docx,zip,mp3,bmp,csv,xls,xlsx,ppt,pptx,pdf,mp4,doc,mpga,wav,webp'
    ),
    'post_max_size' => env(
        'MAX_SIZE_FILE',
        Constant::MAX_SIZE_FILE
    )
];
