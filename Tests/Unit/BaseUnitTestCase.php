<?php

namespace Lexik\Bundle\CurrencyBundle\Tests\Unit;

use Lexik\Bundle\CurrencyBundle\Tests\Fixtures\CurrencyData;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Base unit test class providing functions to create a mock entity manger, load schema and fixtures.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
abstract class BaseUnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Create the database schema.
     *
     * @param EntityManager $em
     */
    protected function createSchema(EntityManager $em)
    {
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Load test fixtures.
     *
     * @param EntityManager $om
     */
    protected function loadFixtures(EntityManager $em)
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);

        $executor->execute(array(new CurrencyData()), false);
    }

    /**
     * EntityManager mock object together with annotation mapping driver and
     * pdo_sqlite database in memory
     *
     * @return EntityManager
     */
    protected function getMockSqliteEntityManager()
    {
        $cache = new \Doctrine\Common\Cache\ArrayCache();

        // xml driver
        $xmlDriver = new \Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver(array(
            __DIR__.'/../../Resources/config/doctrine' => 'Lexik\Bundle\CurrencyBundle\Entity',
        ));

        // configuration mock
        $config = $this->getMock('Doctrine\ORM\Configuration');
        $config->expects($this->any())
            ->method('getMetadataCacheImpl')
            ->will($this->returnValue($cache));
        $config->expects($this->any())
            ->method('getQueryCacheImpl')
            ->will($this->returnValue($cache));
        $config->expects($this->once())
            ->method('getProxyDir')
            ->will($this->returnValue(sys_get_temp_dir()));
        $config->expects($this->once())
            ->method('getProxyNamespace')
            ->will($this->returnValue('Proxy'));
        $config->expects($this->once())
            ->method('getAutoGenerateProxyClasses')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getMetadataDriverImpl')
            ->will($this->returnValue($xmlDriver));
        $config->expects($this->any())
            ->method('getClassMetadataFactoryName')
            ->will($this->returnValue('Doctrine\ORM\Mapping\ClassMetadataFactory'));
        $config->expects($this->any())
            ->method('getDefaultRepositoryClassName')
            ->will($this->returnValue('Doctrine\ORM\EntityRepository'));
        $config->expects($this->any())
            ->method('getRepositoryFactory')
            ->will($this->returnValue(new \Doctrine\ORM\Repository\DefaultRepositoryFactory()));
        $config->expects($this->any())
            ->method('getQuoteStrategy')
            ->will($this->returnValue(new \Doctrine\ORM\Mapping\DefaultQuoteStrategy()));

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $em = \Doctrine\ORM\EntityManager::create($conn, $config);

        return $em;
    }

    protected  function getMockDoctrine()
    {
        $em = $this->getMockSqliteEntityManager();

        $doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $doctrine->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($em));

        return $doctrine;
    }

    protected function getEntityManager()
    {
        $em = $this->doctrine->getManager();

        return $em;
    }
}