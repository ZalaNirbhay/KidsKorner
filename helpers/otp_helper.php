<?php

/**
 * Helper utilities for managing short-lived OTP codes that secure
 * sensitive account actions such as password resets and profile changes.
 */

if (!function_exists('kk_ensure_otp_table')) {
    /**
     * Create the OTP table when it does not already exist.
     */
    function kk_ensure_otp_table(mysqli $con): void
    {
        $createTableSql = "
            CREATE TABLE IF NOT EXISTS `password_reset_codes` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `otp_hash` VARCHAR(255) NOT NULL,
                `purpose` ENUM('password_change', 'forgot_password') NOT NULL,
                `expires_at` DATETIME NOT NULL,
                `is_used` TINYINT(1) DEFAULT 0,
                `used_at` DATETIME NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user_purpose` (`user_id`, `purpose`),
                CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `registration`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        mysqli_query($con, $createTableSql);
    }
}

if (!function_exists('kk_generate_otp')) {
    /**
     * Generate a numeric OTP and persist its hash for later verification.
     */
    function kk_generate_otp(mysqli $con, int $userId, string $email, string $purpose, int $validMinutes = 10): ?array
    {
        kk_ensure_otp_table($con);

        $emailEscaped = mysqli_real_escape_string($con, $email);
        $purposeEscaped = mysqli_real_escape_string($con, $purpose);

        // Optional: remove old/used records for same purpose
        mysqli_query(
            $con,
            "DELETE FROM `password_reset_codes`
             WHERE `user_id` = {$userId}
               AND `purpose` = '{$purposeEscaped}'
               AND (`is_used` = 1 OR `expires_at` < NOW())"
        );

        $otpCode = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = password_hash($otpCode, PASSWORD_DEFAULT);
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$validMinutes} minutes"));

        $insertSql = "
            INSERT INTO `password_reset_codes` (`user_id`, `email`, `otp_hash`, `purpose`, `expires_at`)
            VALUES ({$userId}, '{$emailEscaped}', '{$otpHash}', '{$purposeEscaped}', '{$expiresAt}')
        ";

        if (mysqli_query($con, $insertSql)) {
            return [
                'code' => $otpCode,
                'expires_at' => $expiresAt,
            ];
        }

        return null;
    }
}

if (!function_exists('kk_verify_otp')) {
    /**
     * Verify the OTP entered by the user.
     */
    function kk_verify_otp(mysqli $con, int $userId, string $email, string $purpose, string $submittedCode): array
    {
        kk_ensure_otp_table($con);

        $emailEscaped = mysqli_real_escape_string($con, $email);
        $purposeEscaped = mysqli_real_escape_string($con, $purpose);
        $submittedCode = trim($submittedCode);

        $query = "
            SELECT * FROM `password_reset_codes`
            WHERE `user_id` = {$userId}
              AND `email` = '{$emailEscaped}'
              AND `purpose` = '{$purposeEscaped}'
              AND `is_used` = 0
            ORDER BY `id` DESC
            LIMIT 1
        ";

        $result = mysqli_query($con, $query);
        $record = $result ? mysqli_fetch_assoc($result) : null;

        if (!$record) {
            return ['success' => false, 'message' => 'No OTP request found. Please request a new code.'];
        }

        if (strtotime($record['expires_at']) < time()) {
            mysqli_query($con, "UPDATE `password_reset_codes` SET `is_used` = 1 WHERE `id` = {$record['id']}");
            return ['success' => false, 'message' => 'OTP expired. Please request a new code.'];
        }

        if (!password_verify($submittedCode, $record['otp_hash'])) {
            return ['success' => false, 'message' => 'Invalid OTP. Please check the code and try again.'];
        }

        mysqli_query(
            $con,
            "UPDATE `password_reset_codes`
             SET `is_used` = 1, `used_at` = NOW()
             WHERE `id` = {$record['id']}"
        );

        return ['success' => true, 'message' => 'OTP verified successfully.'];
    }
}


