<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;

class LeilaoDaoTest extends TestCase
{
    private $pdo;

    public function setUp() : void 
    {
        $this->pdo = ConnectionCreator::getConnection();
    }

    public function testInsercaoEBuscaDevemFuncionar()
    {
        $leilao = new Leilao('Variante 0KM');
        $leilaoDao = new LeilaoDao($this->pdo);

        $leilaoDao->salva($leilao);

        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Variante 0KM', $leiloes[0]->recuperarDescricao());
    }

    public function tearDown() : void
    {
        $this->pdo->exec('DELETE FROM leiloes;');
    }
}