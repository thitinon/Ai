<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('THB');
            $table->enum('status', ['pending','paid','refunded','failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_ref')->nullable(); // gateway reference id
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id','status']);
        });

        // order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->timestamps();

            $table->index('order_id');
        });

        // coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent','fixed'])->default('percent');
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('min_purchase', 12, 2)->nullable();
            $table->unsignedBigInteger('max_uses')->nullable();
            $table->unsignedBigInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // refund_requests
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['order_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
