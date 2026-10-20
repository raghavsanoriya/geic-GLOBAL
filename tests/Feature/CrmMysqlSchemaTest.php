<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmMysqlSchemaTest extends TestCase
{
    public function test_required_and_nullable_utc_columns_keep_their_precision_and_explicit_defaults(): void
    {
        config(['database.connections.schema_fixture' => array_replace(config('database.connections.mysql'), ['url' => null, 'host' => 'unreachable.invalid', 'database' => 'schema_fixture'])]);
        $connection = DB::connection('schema_fixture');
        $blueprint = new Blueprint($connection, 'crm_schema_fixture', function (Blueprint $table): void {
            $table->create();
            $table->timestamp('occurred_at');
            $table->timestamp('recorded_at');
            $table->timestampTz('precise_at', 6)->nullable();
            $table->timestamp('default_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });

        $sql = implode(';', $blueprint->toSql());

        $this->assertStringContainsString('`occurred_at` datetime not null', $sql);
        $this->assertStringContainsString('`recorded_at` datetime not null', $sql);
        $this->assertStringContainsString('`precise_at` datetime(6) null', $sql);
        $this->assertStringContainsString('`default_at` datetime not null default CURRENT_TIMESTAMP', $sql);
        $this->assertStringContainsString('`updated_at` datetime null on update CURRENT_TIMESTAMP', $sql);
        $this->assertStringNotContainsString('0000-00-00', $sql);
        DB::purge('schema_fixture');
    }

    public function test_unrelated_mysql_connections_keep_the_standard_schema_types(): void
    {
        config(['database.connections.schema_fixture' => array_replace(config('database.connections.mysql'), ['url' => null, 'host' => 'unreachable.invalid', 'database' => 'schema_fixture', 'utc_datetime_columns' => false])]);
        $connection = DB::connection('schema_fixture');
        $connection->getSchemaBuilder();
        $blueprint = new Blueprint($connection, 'external_fixture', function (Blueprint $table): void {
            $table->create();
            $table->timestamp('occurred_at')->nullable();
        });

        $sql = implode(';', $blueprint->toSql());

        $this->assertStringContainsString('`occurred_at` timestamp null', $sql);
        DB::purge('schema_fixture');
    }
}
