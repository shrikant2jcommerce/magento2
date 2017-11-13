<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Console\Command;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;
use Symfony\Component\Console\Command\Command;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;

/**
 * An Abstract class for Indexer related commands.
 */
abstract class AbstractIndexerCommand extends Command
{
    /**
     * @var ObjectManagerFactory
     */
    private $objectManagerFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var IndexerCollectionFactory|null
     */
    private $collectionFactory;

    /**
     * @param ObjectManagerFactory $objectManagerFactory
     * @param IndexerCollectionFactory|null $collectionFactory
     * @throws \LogicException
     */
    public function __construct(
        ObjectManagerFactory $objectManagerFactory,
        IndexerCollectionFactory $collectionFactory = null
    ) {
        $this->objectManagerFactory = $objectManagerFactory;
        parent::__construct();
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Returns all indexers
     *
     * @return IndexerInterface[]
     */
    protected function getAllIndexers()
    {
        if (null === $this->collectionFactory) {
            $this->collectionFactory = $this->getObjectManager()->create(IndexerCollectionFactory::class);
        }
        return $this->collectionFactory->create()->getItems();
    }

    /**
     * Gets initialized object manager
     *
     * @return ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        if (null === $this->objectManager) {
            $area = FrontNameResolver::AREA_CODE;
            $this->objectManager = $this->objectManagerFactory->create($_SERVER);
            /** @var \Magento\Framework\App\State $appState */
            $appState = $this->objectManager->get(State::class);
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get(ConfigLoaderInterface::class);
            $this->objectManager->configure($configLoader->load($area));
        }
        return $this->objectManager;
    }
}
