<?php
// Fungsi untuk mengatur pesan flash (disimpan di session)
function set_message($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Fungsi untuk menampilkan pesan flash dan menghapusnya dari session
function display_message() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . htmlspecialchars($_SESSION['message_type']) . ' mt-3">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Fungsi untuk mengecek apakah user sudah login
function is_logged_in() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Fungsi untuk redirect jika tidak login
function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Fungsi untuk mengamankan input dari serangan XSS (Cross-Site Scripting)
function escape_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>