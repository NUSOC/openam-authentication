<?php


namespace soc;


use WP_Fatal_Error_Handler;

class OpenAM2020CustomError extends WP_Fatal_Error_Handler
{

    public function DisplayCustomOpenAM2020ErrorAndDie(string $title, string $error) {
        echo sprintf("<h2>Login Error</h2><p>%s</p>", $error);

        error_log($error);
        $this->display_error_template(error_get_last(), true);
        die();
    }

}