<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use Fluent\Orm\Model;
use Fluent\Orm\Support\Carbon;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentIrregularPluralTest extends TestCase
{
    protected function setUp(): void
    {
        $this->createSchema();
    }

    public function createSchema()
    {
        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('irregular_plural_humans', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
        ])
        ->addPrimaryKey('id')
        ->createTable('irregular_plural_tokens', true);

        $this->schema()->addField([
            'irregular_plural_human_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'irregular_plural_token_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
        ])
        ->createTable('irregular_plural_human_irregular_plural_token', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
        ])
        ->addPrimaryKey('id')
        ->createTable('irregular_plural_mottoes', true);

        $this->schema()->addField([
            'irregular_plural_motto_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'cool_motto_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'cool_motto_type' => ['type' => 'varchar', 'constraint' => 255],
        ])
        ->createTable('cool_mottoes', true);
    }

    protected function tearDown(): void
    {
        $this->schema()->dropTable('irregular_plural_tokens');
        $this->schema()->dropTable('irregular_plural_humans');
        $this->schema()->dropTable('irregular_plural_human_irregular_plural_token');
    }

    protected function schema()
    {
        return Config::forge();
    }

    /** @test */
    public function itPluralizesTheTableName()
    {
        $model = new IrregularPluralHuman;

        $this->assertSame('irregular_plural_humans', $model->getTable());
    }

    // /** @test */
    // public function itTouchesTheParentWithAnIrregularPlural()
    // {
    //     Carbon::setTestNow('2018-05-01 12:13:14');

    //     IrregularPluralHuman::create(['email' => 'taylorotwell@gmail.com']);

    //     IrregularPluralToken::insertBatch([
    //         ['title' => 'The title'],
    //     ]);

    //     $human = IrregularPluralHuman::query()->first();

    //     $tokenIds = IrregularPluralToken::pluck('id');

    //     Carbon::setTestNow('2018-05-01 15:16:17');

    //     $human->irregularPluralTokens()->sync($tokenIds);

    //     $human->refresh();

    //     $this->assertSame('2018-05-01 12:13:14', (string) $human->created_at);
    //     $this->assertSame('2018-05-01 15:16:17', (string) $human->updated_at);
    // }

    /** @test */
    public function itPluralizesMorphToManyRelationships()
    {
        $human = IrregularPluralHuman::create(['email' => 'bobby@example.com']);

        $human->mottoes()->create(['name' => 'Real eyes realize real lies']);

        $motto = IrregularPluralMotto::query()->first();

        $this->assertSame('Real eyes realize real lies', $motto->name);
    }
}

class IrregularPluralHuman extends Model
{
    protected $guarded = [];

    public function irregularPluralTokens()
    {
        return $this->belongsToMany(
            IrregularPluralToken::class,
            'irregular_plural_human_irregular_plural_token',
            'irregular_plural_token_id',
            'irregular_plural_human_id'
        );
    }

    public function mottoes()
    {
        return $this->morphToMany(IrregularPluralMotto::class, 'cool_motto');
    }
}

class IrregularPluralToken extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $touches = [
        'irregularPluralHumans',
    ];
}

class IrregularPluralMotto extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function irregularPluralHumans()
    {
        return $this->morphedByMany(IrregularPluralHuman::class, 'cool_motto');
    }
}