<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}
$bizzmagsmarketplace_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4xIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggc3R5bGU9Im9wYWNpdHk6MSIgZmlsbD0iI2ZlZmZmZSIgZD0iTTMyIDMySDQ4MGMxNy43IDAgMzIgMTQuMyAzMiAzMlY5NmMwIDE3LjctMTQuMyAzMi0zMiAzMkgzMkMxNC4zIDEyOCAwIDExMy43IDAgOTZWNjRDMCA0Ni4zIDE0LjMgMzIgMzIgMzJ6bTAgMTI4SDQ4MFY0MTZjMCAzNS4zLTI4LjcgNjQtNjQgNjRIOTZjLTM1LjMgMC02NC0yOC43LTY0LTY0VjE2MHptMTI4IDgwYzAgOC44IDcuMiAxNiAxNiAxNkgzMzZjOC44IDAgMTYtNy4yIDE2LTE2cy03LjItMTYtMTYtMTZIMTc2Yy04LjggMC0xNiA3LjItMTYgMTZ6Ii8+PC9zdmc+';
?>