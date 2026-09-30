<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_page_sections', function (Blueprint $table) {
            $table->boolean('info_is_active')->default(false)->after('methods_description');
            $table->string('info_heading')->nullable()->after('info_is_active');
            $table->text('info_description')->nullable()->after('info_heading');
            $table->string('contact_email')->nullable()->after('info_description');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('contact_address')->nullable()->after('contact_phone');
            $table->string('office_hours')->nullable()->after('contact_address');

            $table->boolean('form_is_active')->default(false)->after('office_hours');
            $table->string('form_heading')->nullable()->after('form_is_active');
            $table->text('form_subtitle')->nullable()->after('form_heading');
            $table->string('form_name_label')->nullable()->after('form_subtitle');
            $table->string('form_email_label')->nullable()->after('form_name_label');
            $table->string('form_message_label')->nullable()->after('form_email_label');
            $table->string('form_submit_label')->nullable()->after('form_message_label');
            $table->string('form_success_message')->nullable()->after('form_submit_label');
            $table->unsignedInteger('form_message_max_length')->default(5000)->after('form_success_message');

            $table->boolean('map_is_active')->default(false)->after('form_message_max_length');
            $table->string('map_heading')->nullable()->after('map_is_active');
            $table->text('map_embed_url')->nullable()->after('map_heading');
            $table->string('map_address_label')->nullable()->after('map_embed_url');

            $table->string('chat_badge_label')->nullable()->after('chat_card_is_active');
            $table->string('chat_team_name')->nullable()->after('chat_badge_label');
            $table->string('chat_status_text')->nullable()->after('chat_team_name');
            $table->string('chat_footer_note')->nullable()->after('chat_status_text');
            $table->text('chat_greeting')->nullable()->after('chat_footer_note');
            $table->string('chat_input_placeholder')->nullable()->after('chat_greeting');
            $table->json('chat_quick_replies')->nullable()->after('chat_input_placeholder');

            $table->string('cta_eyebrow')->nullable()->after('cta_is_active');
            $table->string('cta_secondary_label')->nullable()->after('cta_eyebrow');
            $table->string('cta_secondary_href')->nullable()->after('cta_secondary_label');
            $table->string('cta_response_note')->nullable()->after('cta_secondary_href');
            $table->string('cta_trusted_label')->nullable()->after('cta_response_note');
            $table->string('cta_quick_label')->nullable()->after('cta_trusted_label');

            $table->boolean('newsletter_is_active')->default(true)->after('social_is_active');
            $table->string('newsletter_heading')->nullable()->after('newsletter_is_active');
            $table->string('newsletter_subtitle')->nullable()->after('newsletter_heading');
            $table->string('newsletter_placeholder')->nullable()->after('newsletter_subtitle');
            $table->string('newsletter_button_label')->nullable()->after('newsletter_placeholder');
        });
    }

    public function down(): void
    {
        Schema::table('contact_page_sections', function (Blueprint $table) {
            $table->dropColumn([
                'info_is_active', 'info_heading', 'info_description', 'contact_email', 'contact_phone', 'contact_address', 'office_hours',
                'form_is_active', 'form_heading', 'form_subtitle', 'form_name_label', 'form_email_label', 'form_message_label',
                'form_submit_label', 'form_success_message', 'form_message_max_length',
                'map_is_active', 'map_heading', 'map_embed_url', 'map_address_label',
                'chat_badge_label', 'chat_team_name', 'chat_status_text', 'chat_footer_note', 'chat_greeting', 'chat_input_placeholder', 'chat_quick_replies',
                'cta_eyebrow', 'cta_secondary_label', 'cta_secondary_href', 'cta_response_note', 'cta_trusted_label', 'cta_quick_label',
                'newsletter_is_active', 'newsletter_heading', 'newsletter_subtitle', 'newsletter_placeholder', 'newsletter_button_label',
            ]);
        });
    }
};
