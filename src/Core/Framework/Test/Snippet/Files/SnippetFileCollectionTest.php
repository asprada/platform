<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Test\Snippet\Files;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Exception\InvalidSnippetFileException;
use Shopware\Core\Framework\Snippet\Files\de_DE\SnippetFile_de_DE;
use Shopware\Core\Framework\Snippet\Files\en_EN\SnippetFile_en_GB;
use Shopware\Core\Framework\Snippet\Files\SnippetFileCollection;
use Shopware\Core\Framework\Test\Snippet\_fixtures\TestSnippetExtensionFile_de_DE;
use Shopware\Core\Framework\Test\TestCaseHelper\ReflectionHelper;

class SnippetFileCollectionTest extends TestCase
{
    public function testGet(): void
    {
        $collection = $this->getCollection();

        $result_en_GB = $collection->get('messages.en_GB');
        $result_de_DE = $collection->get('messages.de_DE');
        $result_NA = $collection->get('not.available');

        static::assertSame('en_GB', $result_en_GB->getIso());
        static::assertSame('de_DE', $result_de_DE->getIso());
        static::assertNull($result_NA);
    }

    public function testGetIsoList(): void
    {
        $collection = $this->getCollection();

        $expectedResult = ['de_DE', 'en_GB'];
        $result = $collection->getIsoList();

        static::assertArraySubset($expectedResult, $result);
    }

    public function testGetLanguageFilesByIso(): void
    {
        $collection = $this->getCollection();

        $result_en_GB = $collection->getSnippetFilesByIso('en_GB');
        $result_de_DE = $collection->getSnippetFilesByIso('de_DE');
        $result_empty = $collection->getSnippetFilesByIso('na_NA');
        $result_empty_two = $collection->getSnippetFilesByIso('');

        static::assertNotNull($result_en_GB);
        static::assertNotNull($result_de_DE);
        static::assertNotNull($result_empty);
        static::assertNotNull($result_empty_two);

        static::assertCount(1, $result_en_GB);
        static::assertCount(2, $result_de_DE);
        static::assertCount(0, $result_empty);
        static::assertCount(0, $result_empty_two);

        static::assertSame('en_GB', $result_en_GB[0]->getIso());
        static::assertSame('de_DE', $result_de_DE[0]->getIso());
        static::assertSame([], $result_empty);
        static::assertSame([], $result_empty_two);
    }

    public function testGetBaseFileByIso_expectException(): void
    {
        $collection = $this->getCollection();

        $this->expectException(InvalidSnippetFileException::class);

        $collection->getBaseFileByIso('de_AT');
    }

    public function testGetBaseFileByIso(): void
    {
        $collection = $this->getCollection();

        $result_en_GB = $collection->getBaseFileByIso('en_GB');
        $result_de_DE = $collection->getBaseFileByIso('de_DE');

        static::assertSame('en_GB', $result_en_GB->getIso());
        static::assertSame('de_DE', $result_de_DE->getIso());
    }

    public function testGetListSortedByIso(): void
    {
        $collection = $this->getCollection();
        $method = ReflectionHelper::getMethod(SnippetFileCollection::class, 'getListSortedByIso');

        $result = $method->invoke($collection);
        $expectedResult = ['de_DE' => [], 'en_GB' => []];

        static::assertArraySubset($expectedResult, $result);
    }

    private function getCollection(): SnippetFileCollection
    {
        $collection = new SnippetFileCollection(new ArrayCollection([]));
        $collection->add(new TestSnippetExtensionFile_de_DE());
        $collection->add(new SnippetFile_de_DE());
        $collection->add(new SnippetFile_en_GB());

        return $collection;
    }
}
