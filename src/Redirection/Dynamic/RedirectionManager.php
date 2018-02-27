<?php

namespace AppBundle\Redirection\Dynamic;

use AppBundle\Entity\Redirection;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Serializer;
use Psr\Cache\CacheItemPoolInterface;

class RedirectionManager
{
    private $cache;

    private $entityManager;

    private $serializer;

    public function __construct(CacheItemPoolInterface $cache, EntityManagerInterface $entityManager, Serializer $serializer)
    {
        $this->cache = $cache;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public function refreshRedirectionCache(Redirection $redirection): void
    {
        $item = $this->cache
            ->getItem(md5($redirection->getFrom()))
            ->set($this->serializer->serialize($redirection, 'json'))
        ;
        $this->cache->save($item);
    }

    public function optimiseRedirection(Redirection $originRedirection): void
    {
        $redirections = $this->entityManager->getRepository(Redirection::class)->findByTargetUri($originRedirection->getFrom());

        foreach ($redirections as $redirection) {
            if ($redirection->getFrom() === $originRedirection->getTo()) {
                $this->entityManager->remove($redirection);
            } else {
                $this->setValues($redirection, $redirection->getFrom(), $originRedirection->getTo(), $originRedirection->getType());
            }
        }

        $this->entityManager->flush();
    }

    public function setRedirection(string $source, string $target, int $type = 301): Redirection
    {
        if (!$redirection = $this->entityManager->getRepository(Redirection::class)->findOneByOriginUri($source)) {
            $redirection = new Redirection();
            $this->entityManager->persist($redirection);
        }
        $this->setValues($redirection, $source, $target, $type);

        $this->entityManager->flush();

        return $redirection;
    }

    public function removeRedirection(string $source): void
    {
        if ($redirection = $this->entityManager->getRepository(Redirection::class)->findOneByOriginUri($source)) {
            $this->entityManager->remove($redirection);
            $this->entityManager->flush();
            $this->cache->deleteItem(md5($source));
        }
    }

    public function getRedirection(string $source): ?Redirection
    {
        $item = $this->cache->getItem(md5($source));

        if ($item && $item->isHit()) {
            return $this->serializer->deserialize($item->get(), Redirection::class, 'json');
        }

        if ($redirection = $this->entityManager->getRepository(Redirection::class)->findOneByOriginUri($source)) {
            $this->refreshRedirectionCache($redirection);
        }

        return $redirection;
    }

    private function setValues(Redirection $redirection, string $source, string $target, int $type = 301): void
    {
        $redirection->setFrom($source);
        $redirection->setTo($target);
        $redirection->setType($type);
    }
}
