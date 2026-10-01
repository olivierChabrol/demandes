<?php

namespace Models\Tool;

require_once('models/tool/sql.php');

use Models\Tool\Sql;

class WP_Info
{
    private string $server_url = "https://www.i2m.univ-amu.fr/wp-admin/";
    private string $suffix_ajax = "admin-ajax.php?action=";
    private ?string $base_url = null;

    public function __construct()
    {
        $this->base_url = $this->server_url . $this->suffix_ajax;
    }

    public function getBaseUrl(): ?string
    {
        return $this->base_url;
    }

    public function setBaseUrl(?string $base_url): self
    {
        $this->base_url = $base_url;
        return $this;
    }

    public function get_user_id_by_email($email): ?int
    {
        $json_response = file_get_contents( $this->getBaseUrl() . "lab_email_to_user_id&email=" . urlencode($email) );

        if ( $json_response !== false ) {
            $data = json_decode( $json_response, true );
            if ( is_array( $data ) && isset( $data['success'] ) &&$data['success'] === true ) 
            {
				$wp_user_id = $data['data']['user_id'];
			}
        }

        return $wp_user_id ?? null;
    }

    public function get_user_groups_by_wp_user_id($wp_user_id): ?array
    {
        $json_response = file_get_contents( $this->getBaseUrl() . "lab_groups_by_user_id&id=" . urlencode($wp_user_id) );

        if ( $json_response !== false ) {
            $data = json_decode( $json_response, true );
            if ( is_array( $data ) && isset( $data['success'] ) &&$data['success'] === true ) 
            {
                $wp_user_groups = $data['data'];
            }
        }

        return $wp_user_groups ?? null;
    }
}
