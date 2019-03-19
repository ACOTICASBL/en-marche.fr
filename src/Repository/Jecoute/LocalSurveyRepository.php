<?php

namespace AppBundle\Repository\Jecoute;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\Jecoute\LocalSurvey;
use AppBundle\Entity\Jecoute\NationalSurvey;
use AppBundle\Entity\ReferentTag;
use AppBundle\Repository\ReferentTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LocalSurveyRepository extends ServiceEntityRepository
{
    use ReferentTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LocalSurvey::class);
    }

    /**
     * @return LocalSurvey[]
     */
    public function findAllByAdherent(UserInterface $adherent): array
    {
        return $this
            ->createSurveysForAdherentQueryBuilder($adherent)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Adherent|UserInterface $adherent
     */
    public function createSurveysForAdherentQueryBuilder(Adherent $adherent): QueryBuilder
    {
        return $this
            ->createQueryBuilder('survey')
            ->addSelect('questions')
            ->innerJoin('survey.questions', 'questions')
            ->innerJoin('survey.author', 'author')
            ->innerJoin('author.managedArea', 'managedArea')
            ->innerJoin('managedArea.tags', 'tags')
            ->andWhere('tags.code IN (:codes)')
            ->setParameter('codes', array_map(function (ReferentTag $tag) {
                return $tag->getCode();
            }, $adherent->getReferentTags()->toArray()))
            ->andWhere('survey.published = true')
        ;
    }

    /**
     * @return LocalSurvey[]
     */
    public function findAllByAuthor(Adherent $author): array
    {
        $this->checkReferent($author);

        return $this
            ->createQueryBuilder('survey')
            ->addSelect('questions')
            ->innerJoin('survey.questions', 'questions')
            ->andWhere('survey.author = :author')
            ->andWhere('survey INSTANCE OF '.LocalSurvey::class)
            ->setParameter('author', $author)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOnePublishedByUuid(string $uuid): ?NationalSurvey
    {
        return $this
            ->createQueryBuilder('survey')
            ->addSelect('surveyQuestion', 'question', 'choices')
            ->innerJoin('survey.questions', 'surveyQuestion')
            ->innerJoin('surveyQuestion.question', 'question')
            ->leftJoin('question.choices', 'choices')
            ->innerJoin('survey.administrator', 'administrator')
            ->andWhere('survey.uuid = :uuid')
            ->andWhere('survey.published = true')
            ->setParameter('uuid', $uuid)
            ->addOrderBy('surveyQuestion.position', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
