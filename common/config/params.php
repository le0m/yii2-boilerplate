<?php

return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',

    'user.rememberMeDuration' => 3600 * 24 * 30,
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'user.maxFailedLogins' => 5, // max failed login attempts after which a delay will be active
    'user.failedLoginsDelay' => 60 * 15, // (seconds) delay for subsequent login attempts
];
