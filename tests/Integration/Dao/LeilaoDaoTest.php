<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;

class LeilaoDaoTest extends TestCase
{
    private static $pdo;

    public static function setUpBeforeClass() : void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('create table leiloes (
            id INTEGER primary key,
            descricao TEXT,
            finalizado BOOL,
            dataInicio TEXT
        );');
    }

    public function setUp() : void 
    {
        self::$pdo->beginTransaction();
    }

    /** @dataProvider leiloes */
    public function testBuscarLeiloesNaoFinalizados($leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes  as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Variante 0KM', $leiloes[0]->recuperarDescricao());
        self::assertFalse($leiloes[0]->estaFinalizado());
    }

    /** @dataProvider leiloes */
    public function testBuscarLeiloesFinalizados($leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes  as $leilao) {
            $leilaoDao->salva($leilao);
        }

        $leiloes = $leilaoDao->recuperarFinalizados();

        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Fiat 147 0KM', $leiloes[0]->recuperarDescricao());
        self::assertTrue($leiloes[0]->estaFinalizado());
    }

    public function tearDown() : void
    {
        self::$pdo->rollBack();
    }

    public function leiloes()
    {
        $naoFinalizado = new Leilao('Variante 0KM');
        $finalizado = new Leilao('Fiat 147 0KM');
        $finalizado->finaliza();

        return [
            [
                [$naoFinalizado, $finalizado]
            ]
        ];
    }
}