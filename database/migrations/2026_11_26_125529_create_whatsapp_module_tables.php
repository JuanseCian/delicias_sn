<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction', 20)->default('outbound');
            $table->string('type', 40)->default('text');
            $table->string('recipient', 40)->nullable();
            $table->text('body')->nullable();
            $table->string('status', 30)->default('queued');
            $table->string('provider_message_id')->nullable()->index();
            $table->string('automation_key', 80)->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unique(['order_id', 'automation_key']);
            $table->timestamps();
        });

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('name');
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('whatsapp_automations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('name');
            $table->string('trigger', 80);
            $table->foreignId('template_id')->nullable()->constrained('whatsapp_templates')->nullOnDelete();
            $table->unsignedInteger('delay_minutes')->default(0);
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique();
            $table->string('event_name')->nullable();
            $table->json('payload');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('whatsapp_consent_at')->nullable()->after('phone');
            $table->string('whatsapp_consent_ip', 45)->nullable()->after('whatsapp_consent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_webhook_events');
        Schema::dropIfExists('whatsapp_settings');
        Schema::dropIfExists('whatsapp_automations');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('whatsapp_messages');
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['whatsapp_consent_at', 'whatsapp_consent_ip']);
        });
    }
};
