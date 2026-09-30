<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contact_page_sections')) {
            return;
        }

        Schema::table('contact_page_sections', function (Blueprint $table) {
            $columns = [
                'info_is_active', 'info_heading', 'info_description',
                'contact_email', 'contact_phone', 'contact_address', 'office_hours',
                'form_is_active', 'form_heading', 'form_subtitle',
                'form_name_label', 'form_email_label', 'form_message_label',
                'form_submit_label', 'form_success_message', 'form_message_max_length',
                'map_is_active', 'map_heading', 'map_embed_url', 'map_address_label',
                'chat_badge_label', 'chat_team_name', 'chat_status_text', 'chat_footer_note', 'chat_quick_replies',
                'cta_eyebrow', 'cta_secondary_label', 'cta_secondary_href', 'cta_response_note',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('contact_page_sections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        //
    }
};
